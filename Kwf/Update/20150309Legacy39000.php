<?php
class Kwf_Update_20150309Legacy39000 extends Kwf_Update
{
    private $_countUploads;

    public function getProgressSteps()
    {
        $ret = count(Kwf_Model_Abstract::findAllInstances());
        if ($this->countUploads() < 5000) {
            $ret += $this->countUploads()*2;
        }
        $ret++; // Image filenames
        return $ret;
    }

    public function countUploads()
    {
        if (is_null($this->_countUploads)) {
            if (in_array('kwf_uploads', Kwf_Registry::get('db')->listTables())) {
                $this->_countUploads = Kwf_Registry::get('db')->query('SELECT COUNT(*) FROM kwf_uploads')->fetchColumn();
            } else {
                $this->_countUploads = 0;
            }
        }
        return $this->_countUploads;
    }

    public function update()
    {
        $db = Kwf_Registry::get('db');

        $db->query("SET FOREIGN_KEY_CHECKS = 0");
        $field = $db->fetchRow("SHOW FIELDS FROM `kwf_uploads` WHERE `Field` = 'id'");
        if ($field['Type'] != 'varbinary(36)') {
            $indexes = $db->fetchAll("SELECT
                TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME='kwf_uploads' AND REFERENCED_COLUMN_NAME='id'");
            foreach ($indexes as $index) {
                $sql = "ALTER TABLE {$index['TABLE_NAME']} DROP FOREIGN KEY `{$index['CONSTRAINT_NAME']}`";
                $db->query($sql);
            }

            $indexes = $db->fetchAll("SELECT
                TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME='kwf_uploads' AND REFERENCED_COLUMN_NAME='id'");
            foreach ($indexes as $index) {
                $sql = "ALTER TABLE {$index['TABLE_NAME']} DROP FOREIGN KEY `{$index['CONSTRAINT_NAME']}`";
                $db->query($sql);
            }
            $db->query("ALTER TABLE `kwf_uploads` CHANGE  `id`  `id_old` INT( 11 )");
            $db->query("ALTER TABLE `kwf_uploads` DROP PRIMARY KEY");
            $db->query("ALTER TABLE `kwf_uploads` MODIFY `id_old` INT( 11 ) NULL");
            $db->query("ALTER TABLE `kwf_uploads` ADD  `id` VARBINARY( 36 ) NOT NULL FIRST");
            $db->query("UPDATE `kwf_uploads` SET `id` = UUID()");
            $db->query("ALTER TABLE `kwf_uploads` ADD PRIMARY KEY(`id`)");
        }

        $uploadIds = $db->fetchPairs('SELECT id_old, id FROM kwf_uploads');
        foreach (Kwf_Model_Abstract::findAllInstances() as $model) {
            $this->_progressBar->next(1, 'updating uploads '.get_class($model));
            foreach ($model->getReferences() as $rule) {
                $reference = $model->getReference($rule);
                $refModel = '';
                if (isset($reference['refModel'])) {
                    $refModel = $reference['refModel'];
                } else if (isset($reference['refModelClass'])) {
                    $refModel = $reference['refModelClass'];
                }
                if (!is_instance_of($refModel, 'Kwf_Uploads_Model')) continue;

                $tableName = '';
                if (is_instance_of($model, 'Kwf_Model_Proxy')) {
                    $tableName = $model->getProxyModel()->getTableName();
                } else {
                    $tableName = $model->getTableName();
                }

                $tableExists = $db->fetchOne("SHOW TABLES LIKE '{$tableName}'");
                if (!$tableExists) continue;

                $oldColumnName = $reference['column'] . '_old';
                $columnName = $reference['column'];

                $columnExists = $db->fetchOne("SHOW COLUMNS FROM `{$tableName}` LIKE '{$columnName}'");
                if (!$columnExists) continue;

                $field = $db->fetchRow("SHOW FIELDS FROM `$tableName` WHERE `Field` = '$columnName'");
                if ($field['Type'] != 'varbinary(36)') {
                    $db->beginTransaction();
                    try {
                        $db->query("ALTER TABLE `{$tableName}` CHANGE  `{$columnName}`  `{$oldColumnName}` INT( 11 ) NULL;");
                        $db->query("ALTER TABLE  `{$tableName}` ADD  `{$columnName}` VARBINARY( 36 ) NULL AFTER  `{$oldColumnName}`");
                        $existingIds = $db->fetchCol("SELECT {$oldColumnName} FROM `{$tableName}` WHERE {$oldColumnName}!=''");
                        $ids = array_intersect_key($uploadIds, array_flip($existingIds));
                        foreach (array_chunk($ids, 1000, true) as $chunkedIds) {
                            $values = array();
                            foreach ($chunkedIds as $key => $val) {
                                $values[] = "WHEN '$key' THEN '$val'";
                            }
                            $sql = "UPDATE {$tableName} SET {$columnName}=CASE {$oldColumnName} " . implode(' ', $values) . " END WHERE {$oldColumnName} IN (" . implode(', ', array_keys($chunkedIds)) . ")";
                            $db->query($sql);
                        }
                        $db->query("ALTER TABLE `{$tableName}` DROP `{$oldColumnName}`");
                        $db->query("ALTER TABLE `{$tableName}` ADD INDEX (`{$columnName}`)");
                        $db->query("ALTER TABLE `$tableName` ADD FOREIGN KEY (`$columnName`) REFERENCES `kwf_uploads` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE");
                        $db->commit();
                    } catch (Zend_Db_Exception $e) {
                        $db->rollBack();
                    }
                }
            }
        }
        $db->query("SET FOREIGN_KEY_CHECKS = 1\n");

        $this->_progressBar->next(1, 'updating Image filenames');
        if (in_array('kwc_basic_image', $db->listTables())) {
            $m = Kwf_Model_Abstract::getInstance('Kwc_Abstract_Image_Model');
            $s = new Kwf_Model_Select();
            $s->whereEquals('filename', '');
            $it = new Kwf_Model_Iterator_Packages(
                new Kwf_Model_Iterator_Rows(Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model'), $s)
            );
            foreach ($it as $row) {
                $pr = $m->getRow($m->select()->whereEquals('kwf_upload_id', $row->id));
                if ($pr) {
                    $row->filename = $pr->filename;
                    $row->save();
                }
            }
        }

        $field = $db->fetchRow("SHOW FIELDS FROM `kwf_uploads` WHERE `Field` = 'md5_hash'");
        if (!$field) {
            $db->query("ALTER TABLE  `kwf_uploads` ADD  `md5_hash` VARCHAR( 32 ) NOT NULL");
            $db->query("ALTER TABLE  `kwf_uploads` ADD INDEX  `md5_hash` (  `md5_hash` )");
        }

        if ($this->countUploads() < 5000) {
            $this->renameUploads();
            $this->createHashes();
            $this->moveOldFiles();
        } else {
            echo "More than 5000 Uploads. Please execute renaming manually:\n\"php bootstrap.php update-uploads rename-uploads\"\n\"php bootstrap.php update-uploads create-hashes\"\n\"php bootstrap.php update-uploads move-old-files\"\n";
        }
    }

    public function renameUploads()
    {
        $db = Kwf_Registry::get('db');
        $uploadsDir = Kwf_Config::getValue('uploads');
        if (file_exists($uploadsDir . '/mediaprescale')) {
            rename($uploadsDir . '/mediaprescale', $uploadsDir . '/mediaprescaleold');
        } else {
            mkdir($uploadsDir . '/mediaprescaleold');
        }
        mkdir($uploadsDir . '/mediaprescale');
        $uploadIds = $db->fetchPairs('SELECT id_old, id FROM kwf_uploads');
        foreach ($uploadIds as $oldId => $id) {
            $this->_progressBar->next(1, 'renaming upload '.$id);
            $this->_renameUploads($uploadsDir . '/', $oldId, $id);
        }
        $this->_deldir($uploadsDir . '/mediaprescaleold');
    }

    private function _renameUploads($path, $oldName, $newName)
    {
        if (is_file($path . $oldName) || is_file($path . $oldName . '_old')) {
            $foldername = substr($newName,0,2);
            if (is_file($path . $foldername)) {
                rename($path . $foldername, $path . $foldername . '_old');
            }

            $filename = $oldName;
            if (is_file($path . $filename . '_old')) {
                $filename .= '_old';
            }

            if (!is_dir($path . $foldername)) {
                mkdir($path . $foldername);
            }

            rename($path . $filename, $path . $foldername . '/' . $newName);
            if (file_exists($path . 'mediaprescaleold/' . $oldName)) {
                if (!is_dir($path . 'mediaprescale/' .$foldername)) {
                    mkdir($path . 'mediaprescale/' .$foldername);
                }

                rename($path . 'mediaprescaleold/' . $oldName, $path . 'mediaprescale/' . $foldername . '/' . $newName);
            }
        }
    }

    public function createHashes()
    {
        $db = Kwf_Registry::get('db');
        $s = new Kwf_Model_Select();
        $it = new Kwf_Model_Iterator_Packages(
            new Kwf_Model_Iterator_Rows(Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model'), $s)
        );
        foreach ($it as $row) {
            $this->_progressBar->next(1, 'calculating md5 '.$row->id);
            $md5Hash = $db->query("SELECT md5_hash FROM kwf_uploads WHERE  `id` = '{$row->id}'")->fetchColumn();
            if (!$md5Hash && file_exists($row->getFileSource())) {
                $md5Hash = md5_file($row->getFileSource());
                $db->query("UPDATE `kwf_uploads` SET  `md5_hash` =  '{$md5Hash}' WHERE  `id` = '{$row->id}';");
            }
        }
    }

    public function moveOldFiles()
    {
        $uploadsDir = Kwf_Config::getValue('uploads');
        if (!is_dir($uploadsDir . '/old')) mkdir($uploadsDir . '/old');
        if (!is_dir($uploadsDir . '/old/mediaprescale')) mkdir($uploadsDir . '/old/mediaprescale');
        foreach (glob($uploadsDir.'/*') as $file) {
            if (!is_file($file)) continue;
            rename($file, $uploadsDir . '/old/' . substr($file, strrpos($file, '/')+1));
        }
        foreach (glob($uploadsDir.'/mediaprescale/*') as $file) {
            if (!is_file($file)) continue;
            rename($file, $uploadsDir . '/old/mediaprescale' . substr($file, strrpos($file, '/')+1));
        }
    }

    private function _deldir($dir)
    {
        $current_dir = opendir($dir);
        while ($entryname = readdir($current_dir)){
            if (is_dir("$dir/$entryname") && ($entryname != "." && $entryname!="..")) {
                $this->_deldir("${dir}/${entryname}");
            } else if ($entryname != "." && $entryname!="..") {
                unlink("${dir}/${entryname}");
            }
        }
        closedir($current_dir);
        rmdir($dir);
    }
}
