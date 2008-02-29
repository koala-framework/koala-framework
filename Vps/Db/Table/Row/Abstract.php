<?php
abstract class Vps_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    protected $_cacheImages = array();
    const FILE_PASSWORD = 'l4Gx8SFe';
    const FILE_PASSWORD_DOWNLOAD = 'j3yjEdv1';

    /**
     * Gibt einen von Sonderzeichen befreiten und eindeutigen String zurück.
     *
     * Ersetzt alle Zeichen außer a-z0-9_ möglichst sinngemäß, auch im kyrillischen
     * Zeichensatz (falls die transliterate-Erweiterung installiert ist). Optional
     * kann der String auf Eindeutigkeit in einer Tabelle geändert werden. Falls der
     * gleiche String schon existiert, wird _1, _2 ... angehängt.
     *
     * @param string String, der Unique sein sollte
     * @param string Spaltenname, dessen Werte unique sein sollten
     * @param string Where-Klausel für Unique-Abfrage (zB. 'parent_id=1')
     * @return string Unique String
     */
    public function getUniqueString($string, $fieldname = '', array $where = array())
    {
        // Sonderzeichen rausnehmen
        $string = Vps_Filter::get($string, 'Url');

        // Unique machen
        if ($fieldname != '') {
            $primaryKey = key($this->_getPrimaryKey());
            $primaryValue = current($this->_getPrimaryKey());
            $where["$primaryKey != ?"] = $primaryValue;

            $x = 0;
            $unique = $string;
            $where["$fieldname = ?"] = $unique;
            while ($this->getTable()->fetchAll($where)->count() > 0) {
                $unique = $string . '_' . ++$x;
                $where["$fieldname = ?"] = $unique;
            }
            $string = $unique;
        }

        return $string;
    }

    /**
     * Speichert die Nummerierung für einen Datensatz und passt die restlichen
     * Datensätze an.
     *
     * @param string Spaltenname, in der die Nummerierung steht
     * @param int Nummer des zu nummerierenden Datensatzes
     * @param string Where-Klausel für Einschränkung der betreffenden Datensätze (zB. 'parent_id=1')
     * @return boolean Ob Nummerierung erfolgreich war
     */
    public function numberize($fieldname, $value = null, array $where = array())
    {
        $originalWhere = $where;
        $primaryKey = key($this->_getPrimaryKey());
        $primaryValue = current($this->_getPrimaryKey());
        $where["$primaryKey != ?"] = $primaryValue;

        // Wenn value null ist, Datensatz am Ende einfügen
        if (is_null($value)) {
            $value = $this->getTable()->fetchAll($where)->count() + 1;
        }

        $x = 0;
        foreach ($this->getTable()->fetchAll($where, $fieldname) as $row) {
            $x++;
            if ($x == $value) $x++;
            $row->$fieldname = $x;
            $row->save();
        }

        $this->$fieldname = $value;
        $this->save();

        $this->getTable()->numberizeAll($fieldname, $originalWhere);
    }

    public function duplicate($data = array())
    {
        $data = array_merge($this->toArray(), $data);
        unset($data['id']);
        $new = $this->getTable()->createRow($data);
        $new->save();
        return $new;
    }

    protected function _duplicateParentRow($tableClassname, $ruleKey = null)
    {
        $row = $this->findParentRow($tableClassname, $ruleKey);
        $new = $row->duplicate();
        $ref = $this->getTable()->getReference($tableClassname, $ruleKey);
        $data = array();
        foreach ($ref['columns'] as $k=>$c) {
            $this->$c = $new->{$ref['refColumns'][$k]};
        }
        $this->save();
    }

    protected function _duplicateDependentTable($tableClassname, $ruleKey = null)
    {
        $rowset = $this->findDependentRowset($tableClassname, $ruleKey);
        foreach ($rowset as $row) {
            $ref = $row->getTable()->getReference($tableClassname, $ruleKey);
            $data = array();
            foreach ($ref['columns'] as $k=>$c) {
                $data[$ref['refColumns'][$k]] = $this->$c;
            }
            $row->duplicate($data);
        }
    }

    // Dateihandling
    public function getFileSource($rule = null, $type = 'default')
    {
        $rule = $this->_getRule($rule);
        $fileRow = $this->findParentRow('Vps_Dao_File', $rule);

        if (!$fileRow) {
            return null;
        }

        $uploadDir = Vps_Dao_Row_File::getUploadDir();
        $uploadId = $fileRow->id;
        $class = get_class($this->getTable());
        $id = $this->_getIdString();
        $target = "$uploadDir/cache/$uploadId/$class.$id.$rule.$type";

        if (!is_file($target)) {
            // Verzeichnisse anlegen, falls nicht existent
            $uploadDir = Vps_Dao_Row_File::getUploadDir();
            if (!is_dir($uploadDir . '/cache')) {
                mkdir($uploadDir . '/cache', 0775);
                chmod($uploadDir . '/cache', 0775);
            }
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0775);
                chmod(dirname($target), 0775);
            }

            // Cache-Datei erstellen
            $source = $fileRow->getFileSource();
            $this->_createCacheFile($source, $target, $type);
        }

        return $target;
    }

    public function getFileUrl($rule = null, $type = 'default', $filename = null, $addRandom = false, $encryption = self::FILE_PASSWORD)
    {
        $rule = $this->_getRule($rule);
        $fileRow = $this->findParentRow('Vps_Dao_File', $rule);
        if (!$fileRow) {
            return null;
        }
        if ($this->getTable() instanceof Vpc_Table) {
            $class = $this->getTable()->getComponentClass();
        } else {
            $class = get_class($this->getTable());
        }
        $id = $this->_getIdString();
        $extension = $fileRow->extension;
        $checksum = md5($encryption . $class . $id . $rule . $type);
        $random = $addRandom ? '?' . uniqid() : '';
        if (!$filename || $filename == '') {
            $filename = $fileRow->filename;
        }
        return "/media/$class/$id/$rule/$type/$checksum/$filename.$extension$random";
    }

    public function getFileSize($rule = null, $type = 'default')
    {
        $target = $this->getFileSource($rule, $type);
        if (is_file($target)) {
            return filesize($target);
        }
        return null;
    }

    public function getFileExtension($rule = null)
    {
        $fileRow = $this->findParentRow('Vps_Dao_File', $rule);
        if ($fileRow) {
            return $fileRow->extension;
        }
        return null;
    }

    public function getImageDimensions($rule = null, $type = 'default')
    {
        $target = $this->getFileSource($rule, $type);
        if (is_file($target)) {
            $size = getimagesize($target);
            return array('width' => $size[0], 'height' => $size[1]);
        }
        return null;
    }

    protected function _createCacheFile($source, $target, $type = 'default')
    {
        if (isset($this->_cacheImages[$type])) {
            $data = $this->_cacheImages[$type];
            $size = array($data[0], $data[1]);
            if (isset($data[2])) {
                Vps_Media_Image::scale($source, $target, $size, $data[2]);
            } else {
                Vps_Media_Image::scale($source, $target, $size);
            }
        } else if ($type == 'thumb') {
            Vps_Media_Image::scale($source, $target, array(100, 100));
        } else {
            symlink($source, $target);
        }
    }

    private function _getRule($rule)
    {
        if (!$rule) {
            $info = $this->getTable()->info();
            foreach ($info['referenceMap'] as $rule => $data) {
                if ($data['refTableClass'] == 'Vps_Dao_File') {
                    return $rule;
                }
            }
        }
        return $rule;
    }

    private function _getIdString()
    {
        return implode(',', $this->_getPrimaryKey());
    }

}
