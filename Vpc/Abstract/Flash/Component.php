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

        // flash data
        $ret['flash']['data'] = $this->_getFlashData();
        if (!array_key_exists('url', $ret['flash']['data'])
            || !array_key_exists('width', $ret['flash']['data'])
            || !array_key_exists('height', $ret['flash']['data'])
        ) {
            throw new Vps_Exception("_getFlashData() must return an array with keys 'url', 'width' and 'height'");
        }
        if (!isset($ret['flash']['data']['params'])) {
            $ret['flash']['data']['params'] = array();
        }
        $ret['flash']['data']['params'] = (object)$ret['flash']['data']['params'];

        // flash vars
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

    /**
     * Muss ein array mit 'url', 'width' und 'height' zurückgeben
     * Optional 'params': ein array mit weiteren parametern die gesetzt
     *                    werden sollen, zB: array('allowfullscreen' => 'true')
     */
    protected function _getFlashData()
    {
        return array();
    }
}
