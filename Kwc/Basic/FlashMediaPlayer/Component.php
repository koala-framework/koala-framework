<?php
class Kwc_Basic_FlashMediaPlayer_Component extends Kwc_Abstract_Flash_Upload_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Flash Media Player');
        $ret['componentCategory'] = 'media';
        $ret['ownModel'] = 'Kwc_Basic_FlashMediaPlayer_Model';
        $ret['playerPath'] = '/assets/kwf/Kwc/Basic/FlashMediaPlayer/player.swf';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();
        $ret['url'] = $this->_getSetting('playerPath');
        $ret['width'] = $this->_getRow()->width;
        $ret['height'] = $this->_getRow()->height;
        $ret['params']['allowfullscreen'] = true;
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
