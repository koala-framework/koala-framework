<?php
abstract class Vps_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    private $_skipFilters = false; //für saveSkipFilters
    protected $_cacheImages = array();
    const FILE_PASSWORD = 'l4Gx8SFe';
    const FILE_PASSWORD_DOWNLOAD = 'j3yjEdv1';

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

        if (!is_file($target) && !is_link($target)) {
            // Verzeichnisse anlegen, falls nicht existent
            Vps_Dao_Row_File::prepareCacheTarget($target);

            // Cache-Datei erstellen
            $source = $fileRow->getFileSource();
            if (file_exists($target)) unlink($target);
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

    public function deleteFileCache($rule = null)
    {
        $fileRow = $this->findParentRow('Vps_Dao_File', $rule);
        if ($fileRow) {
            $fileRow->deleteCache();
        }
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
            if (is_file($target)) @unlink($target);
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
    public function getFileRow($rule = null)
    {
        $rule = $this->_getRule($rule);
        return $this->findParentRow('Vps_Dao_File', $rule);
    }

    private function _getIdString()
    {
        return implode(',', $this->_getPrimaryKey());
    }


    public function toDebug()
    {
        $i = get_class($this);
        if (method_exists($this, '__toString')) {
            $i .= " (".$this->__toString().")\n";
        }
        $ret = print_r($this->_data, true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }

    //f�r Filter_Row_UniqueAscii
    public function getPrimaryKey()
    {
        return $this->_getPrimaryKey();
    }

    protected function _insert()
    {
        parent::_insert();
        $this->_updateFilters();
    }

    protected function _update()
    {
        parent::_update();
        $this->_updateFilters();
    }

    private function _updateFilters()
    {
        if ($this->_skipFilters) return; //für saveSkipFilters

        $filters = $this->getTable()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Vps_Filter_Row_Abstract) {
                $this->$k = $f->filter($this);
            } else {
                $this->$k = $f->filter($this->__toString());
            }
        }
    }


    protected function _delete()
    {
        parent::_delete();
        $filters = $this->getTable()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Vps_Filter_Row_Abstract) {
                $f->onDeleteRow($this);
            }
        }
    }

    protected function _postInsert()
    {
        parent::_postInsert();
        if (Zend_Controller_Front::getInstance() instanceof Vps_Controller_Front_Component) {
            foreach (Vps_Dao::getTable('Vps_Dao_TreeCache')->getComponentClasses() as $c) {
                $tc = Vpc_TreeCache_Abstract::getInstance($c);
                if ($tc) $tc->onInsertRow($this);
            }
            Vpc_TreeCache_Abstract::getTreeCacheTable()->createMissingChilds();
        }
    }

    protected function _postDelete()
    {
        parent::_postDelete();
        if (Zend_Controller_Front::getInstance() instanceof Vps_Controller_Front_Component) {
            foreach (Vps_Dao::getTable('Vps_Dao_TreeCache')->getComponentClasses() as $c) {
                $tc = Vpc_TreeCache_Abstract::getInstance($c);
                if ($tc) $tc->onDeleteRow($this);
            }
            Vpc_TreeCache_Abstract::getTreeCacheTable()->createMissingChilds();
        }
    }

    protected function _postUpdate()
    {
        parent::_postUpdate();
        if (Zend_Controller_Front::getInstance() instanceof Vps_Controller_Front_Component) {
            foreach (Vps_Dao::getTable('Vps_Dao_TreeCache')->getComponentClasses() as $c) {
                $tc = Vpc_TreeCache_Abstract::getInstance($c);
                if ($tc) $tc->onUpdateRow($this);
            }
            Vpc_TreeCache_Abstract::getTreeCacheTable()->createMissingChilds();
        }
    }
    

    //Speichern und abei die Filter nicht verwenden
    //wird benötigt bei der Nummerierung um eine Endlusschleife zu verhindern
    public function saveSkipFilters()
    {
        $this->_skipFilters = true;
        $this->save();
        $this->_skipFilters = false;
    }
}
