<?php
class Vpc_Basic_FlashMediaPlayer_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Flash Media Player');
        $ret['componentIcon'] = new Vps_Asset('film');
        $ret['tablename'] = 'Vpc_Basic_FlashMediaPlayer_Model';
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

        $ret['url'] = $this->_getMediaFileUrl();
        $ret['playerPath'] = $this->_getSetting('playerPath');
        $ret['row'] = $row;

        return $ret;
    }

    private function _getMediaFileUrl()
    {
        $url = $this->_getRow()->getFileUrl(null, 'default', 'unnamed');
        list($url) = explode('?', $url);
        return $url;
    }

    public function hasContent()
    {
        if ($this->_getMediaFileUrl()) return true;
        return false;
    }

}
