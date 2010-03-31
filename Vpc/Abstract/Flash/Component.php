<?php
abstract class Vpc_Abstract_Flash_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Flash');
        $ret['componentIcon'] = new Vps_Asset('film');

        $ret['assets']['files'][] = 'vps/Vpc/Abstract/Flash/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'SwfObject';

        $ret['cssClass'] = 'webStandard';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['flash'] = array();

        $ret['flash']['data'] = $this->_getFlashData();
        if (!isset($ret['flash']['data']['url']) || !isset($ret['flash']['data']['width'])
            || !isset($ret['flash']['data']['height'])
        ) {
            throw new Vps_Exception("_getFlashData() must return an array with keys 'url', 'width' and 'height'");
        }
        $ret['flash']['vars'] = $this->_getFlashVars();
        foreach ($ret['flash']['vars'] as $k => $v) {
            $ret['flash']['vars'][$k] = urlencode($v);
        }
        $ret['flash']['vars'] = (object)$ret['flash']['vars'];

        return $ret;
    }

    protected function _getFlashVars()
    {
        return array();
    }

    protected function _getFlashData()
    {
        return array();
    }
}
