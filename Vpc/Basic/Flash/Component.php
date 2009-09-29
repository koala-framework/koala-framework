<?php
class Vpc_Basic_Flash_Component extends Vpc_Abstract implements Vps_Media_Output_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Flash');
        $ret['componentIcon'] = new Vps_Asset('film');
        $ret['ownModel'] = 'Vpc_Basic_Flash_Model';
        $ret['default'] = array();
        $ret['assets']['dep'][] = 'SwfObject';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['url'] = $this->_getFlashUrl();
        $ret['row'] = $this->_getRow();
        $ret['flashVars'] = $this->_getFlashVars();

        return $ret;
    }

    // extra funktion, damit komponenten im web die flashVars erweitern / überschreiben kann
    protected function _getFlashVars()
    {
        $vars = $this->_getRow()->getChildRows('FlashVars');
        $ret = array();
        if (count($vars)) {
            foreach ($vars as $var) {
                if (!empty($var->key)) {
                    $ret[$var->key] = $var->value;
                }
            }
        }
        return $ret;
    }

    private function _getFlashUrl()
    {
        $row = $this->_getRow();
        $fRow = $row->getParentRow('FileMedia');
        if (!$fRow) {
            return null;
        }
        $filename = $fRow->filename.'.'.$fRow->extension;
        $id = $this->getData()->dbId;
        return Vps_Media::getUrl(get_class($this), $id, 'default', $filename);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $row = Vpc_Abstract::createModel($className)->getRow($id);
        $fileRow = false;
        if ($row) {
            $fileRow = $row->getParentRow('FileMedia');
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
        if ($this->_getFlashUrl()) return true;
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
