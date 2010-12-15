<?php
abstract class Vpc_Abstract_Flash_Upload_Component extends Vpc_Abstract_Flash_Component
    implements Vps_Media_Output_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
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
        return Vps_Media::getUrl(get_class($this), $id, 'default', $filename);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        if (!$c) return null;
        $row = $c->getComponent()->getRow();
        $fileRow = false;
        if ($row) {
            $fileRow = $row->getParentRow(Vpc_Abstract::getSetting($className, 'uploadModelRule'));
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

        if ($row) {
            Vps_Component_Cache::getInstance()->saveMeta(
                get_class($row->getModel()), $row->component_id, $id, Vps_Component_Cache::META_CALLBACK
            );
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

    public function onCacheCallback($row)
    {
        $cacheId = Vps_Media::createCacheId(
            $this->getData()->componentClass, $this->getData()->componentId, 'default'
        );
        Vps_Media::getOutputCache()->remove($cacheId);
    }
}
