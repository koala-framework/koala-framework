<?php
class Vpc_Basic_Image_Row extends Vpc_Row
{
    protected function _createCacheFile($source, $target)
    {
        $dimension = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'dimensions');
        if (isset($dimension[0]) && !is_array($dimension[0])) {
            $s['width'] = $dimension[0];
            $s['height'] = $dimension[1];
            $s['scale'] = isset($dimension[2]) ? $dimension[2] : false;
        } else { // aus DB
            $s['width'] = $this->width;
            $s['height'] = $this->height;
            $s['scale'] = $this->scale;
        }
        if (is_null($s['width']) && is_null($s['height'])) {
            $parentRow = $this->findParentRow('Vps_Dao_File');
            if ($parentRow) {
                $originalFile = $parentRow->getFileSource();
                if (is_file($originalFile)) {
                    $data = getimagesize($originalFile);
                    $s['width'] = $data[0];
                    $s['height'] = $data[1];
                }
            }
        }
        Vps_Media_Image::scale($source, $target, $s);
    }

    public function getFileUrl($rule = null, $type = 'default', $filename = null, $addRandom = false)
    {
        if ($this->filename != '') {
            $filename = $this->filename;
        }
        return parent::getFileUrl($rule, $type, $filename, $addRandom);
    }

    protected function _postUpdate()
    {
        parent::_postUpdate();
        $this->deleteFileCache();
    }

    public function findParentRow($parentTable, $ruleKey = null, Zend_Db_Table_Select $select = null)
    {
        $ret = parent::findParentRow($parentTable, $ruleKey, $select);
        if (is_null($ret)) {
            $filename = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'emptyImage');
            if ($filename) {
                $ext = substr($filename, strrpos($filename, '.') + 1);
                $filename = substr($filename, 0, strrpos($filename, '.'));
                $filename = Vpc_Admin::getComponentFile($this->getTable()->getComponentClass(), $filename, $ext);
                $table = new $parentTable();
                $ret = $table->createRow();
                $ret->extension = $ext;
                $ret->filename = $filename;
            }
        }
        return $ret;
    }
}
