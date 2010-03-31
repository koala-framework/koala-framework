<?php
class Vpc_Basic_FlashMediaPlayer_Component extends Vpc_Abstract_Flash_Upload_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Flash Media Player');
        $ret['ownModel'] = 'Vpc_Basic_FlashMediaPlayer_Model';
        $ret['playerPath'] = '/assets/vps/Vpc/Basic/FlashMediaPlayer/player.swf';
        return $ret;
    }

    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();
        $ret['url'] = $this->_getSetting('playerPath');
        $ret['width'] = $this->_getRow()->width;
        $ret['height'] = $this->_getRow()->height;
        return $ret;
    }

    protected function _getFlashVars()
    {
        $ret = parent::_getFlashVars();
        $ret['file'] = $this->_getUploadUrl();
        if ($this->_getRow()->autostart) {
            $ret['autostart'] = 'true';
        }
        if ($this->_getRow()->loop) {
            $ret['repeat'] = 'always';
        }
        return $ret;
    }
}
