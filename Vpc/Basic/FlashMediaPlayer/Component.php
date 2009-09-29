<?php
class Vpc_Basic_FlashMediaPlayer_Component extends Vpc_Abstract implements Vps_Media_Output_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Flash Media Player');
        $ret['componentIcon'] = new Vps_Asset('film');
        $ret['ownModel'] = 'Vpc_Basic_FlashMediaPlayer_Model';
        $ret['default'] = array(
            'width' => 400,
            'height' => 300
        );
        $ret['assets']['dep'][] = 'SwfObject';
        $ret['playerPath'] = '/assets/vps/Vpc/Basic/FlashMediaPlayer/player.swf';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->_getRow();

        $ret['url'] = $this->_getFlashUrl();
        $ret['playerPath'] = $this->_getSetting('playerPath');
        $ret['row'] = $row;

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

}
