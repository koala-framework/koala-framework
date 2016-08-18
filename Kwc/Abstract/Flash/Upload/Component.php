<?php
abstract class Kwc_Abstract_Flash_Upload_Component extends Kwc_Abstract_Flash_Component
    implements Kwf_Media_Output_Interface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['uploadModelRule'] = 'FileMedia';
        return $ret;
    }

    protected function _getUploadUrl()
    {
        $row = $this->_getRow();
        $fRow = $row->getParentRow($this->_getSetting('uploadModelRule'));
        if (!$fRow) {
            return null;
        }
        $filename = $fRow->filename.'.'.$fRow->extension;
        $id = $this->getData()->componentId;
        return Kwf_Media::getUrl(get_class($this), $id, 'default', $filename);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        if (!$c) return null;
        $row = $c->getComponent()->getRow();
        $fileRow = false;
        if ($row) {
            $fileRow = $row->getParentRow(Kwc_Abstract::getSetting($className, 'uploadModelRule'));
        }
        if (!$fileRow) {
            return null;
        } else {
            $file = $fileRow->getFileSource();
            $mimeType = $fileRow->mime_type;
        }
        if (!$file || !file_exists($file)) {
            return null;
        }

        return array(
            'file' => $file,
            'mimeType' => $mimeType
        );
    }

    public function hasContent()
    {
        if ($this->_getUploadUrl()) return true;
        return false;
    }
}
