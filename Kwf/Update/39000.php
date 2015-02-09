<?php
class Kwf_Update_39000 extends Kwf_Update
{

    public function getProgressSteps()
    {
        $ret = count(Kwf_Model_Abstract::findAllInstances());
        if (in_array('kwf_uploads', Kwf_Registry::get('db')->listTables())) {
            $ret += Kwf_Registry::get('db')->query('SELECT COUNT(*) FROM kwf_uploads')->fetchColumn()*2;
        }
        return $ret;
    }

    public function update()
    {
        $db = Kwf_Registry::get('db');
        $uploadsModel = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model');
        $uploadsDir = $dir = Kwf_Config::getValue('uploads');

        $idColumn = reset($db->fetchAll("SHOW FIELDS FROM `kwf_uploads` WHERE `Field` = 'id'"));
        if ($idColumn['Type'] != 'varbinary(36)') {
            $db->query("SET FOREIGN_KEY_CHECKS = 0\n");
            $db->query("ALTER TABLE  `kwf_uploads` CHANGE  `id`  `id_old` INT( 11 ) NOT NULL");
            $db->query("ALTER TABLE  `kwf_uploads` DROP PRIMARY KEY");
            $db->query("ALTER TABLE  `kwf_uploads` ADD  `id` VARBINARY( 36 ) NOT NULL FIRST");

            $uploadIds = array();
            foreach ($db->query("SELECT id_old FROM `kwf_uploads`")->fetchAll() as $data) {
                $id = Kwf_Filter_GenerateUuid::filter(null);
                if (!isset($uploadIds[$data['id_old']])) $uploadIds[$data['id_old']] = $id;
                $db->query("UPDATE  `kwf_uploads` SET  `id` =  '{$id}' WHERE  `id_old` = {$data['id_old']} LIMIT 1 ;");
            }
            $db->query('ALTER TABLE `kwf_uploads` ADD PRIMARY KEY(`id`)');

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

                    $oldColumnName = $reference['column'] . '_old';
                    $columnName = $reference['column'];

                    $tableName = '';
                    if (is_instance_of($model, 'Kwf_Model_Proxy')) {
                        $tableName = $model->getProxyModel()->getTableName();
                    } else {
                        $tableName = $model->getTableName();
                    }
                    $db->query("ALTER TABLE `{$tableName}` CHANGE  `{$columnName}`  `{$oldColumnName}` INT( 11 ) NULL;");
                    $db->query("ALTER TABLE  `{$tableName}` ADD  `{$columnName}` VARBINARY( 36 ) NULL AFTER  `{$oldColumnName}`");

                    foreach ($db->query("SELECT {$oldColumnName} FROM `{$tableName}`")->fetchAll() as $data) {
                        if (!$data[$oldColumnName]) continue;

                        $id = $uploadIds[$data[$oldColumnName]];
                        $db->query("UPDATE  `{$tableName}` SET  `{$columnName}` =  '{$id}' WHERE  `{$oldColumnName}` = {$data[$oldColumnName]};");
                    }
                    $db->query("ALTER TABLE `{$tableName}` DROP `{$oldColumnName}`");
                }
            }

            if (file_exists($uploadsDir . '/mediaprescale')) {
                rename($uploadsDir . '/mediaprescale', $uploadsDir . '/mediaprescaleold');
            } else {
                mkdir($uploadsDir . '/mediaprescaleold');
            }
            mkdir($uploadsDir . '/mediaprescale');
            $select = new Kwf_Model_Select();
            $it = new Kwf_Model_Iterator_Packages(
                new Kwf_Model_Iterator_Rows($uploadsModel, $select)
            );
            foreach ($it as $row) {
                $this->_progressBar->next(1, 'renaming upload '.$row->id);
                $this->_renameUploads($uploadsDir . '/', $row->id_old, $row->id);
            }
            rmdir($uploadsDir . '/mediaprescaleold');

            //$db->query('ALTER TABLE `kwf_uploads` DROP `id_old`');
            $db->query("SET FOREIGN_KEY_CHECKS = 1\n");
        }

        $db->query("ALTER TABLE  `kwf_uploads` ADD  `md5_hash` VARCHAR( 32 ) NOT NULL");
        $db->query("ALTER TABLE  `kwf_uploads` ADD INDEX  `md5_hash` (  `md5_hash` )");
        $s = new Kwf_Model_Select();
        $it = new Kwf_Model_Iterator_Packages(
            new Kwf_Model_Iterator_Rows($uploadsModel, $s)
        );
        foreach ($it as $row) {
            $this->_progressBar->next(1, 'calculating md5 '.$row->id);
            if (file_exists($row->getFileSource())) {
                $md5Hash = md5_file($row->getFileSource());
                $db->query("UPDATE  `kwf_uploads` SET  `md5_hash` =  '{$md5Hash}' WHERE  `id` = '{$row->id}';");
            }
        }

        if (in_array('kwc_basic_image', $db->listTables())) {
            $m = Kwf_Model_Abstract::getInstance('Kwc_Abstract_Image_Model');
            $s = new Kwf_Model_Select();
            $s->whereEquals('filename', '');
            $it = new Kwf_Model_Iterator_Packages(
                new Kwf_Model_Iterator_Rows(Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model'), $s)
            );
            foreach ($it as $row) {
                $pr = $row->getParentRow('Image');
                if ($pr) {
                    $row->filename = $pr->filename;
                    $row->save();
                }
            }
        }
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
}
