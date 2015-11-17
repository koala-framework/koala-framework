<?php
abstract class Kwc_Abstract_Flash_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Flash');
        $ret['componentIcon'] = 'film';

        $ret['generators']['placeholder'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Abstract_Flash_Placeholder_Component'
        );

        $ret['assets']['dep'][] = 'ExtUtilJson';
        $ret['assets']['dep'][] = 'SwfObject';

        $ret['cssClass'] = 'webStandard';

        $ret['removeHiddenFlash'] = true;

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['flash'] = array();

        // flash data
        $ret['flash']['data'] = $this->_getFlashData();
        if (!array_key_exists('url', $ret['flash']['data'])
            || !array_key_exists('width', $ret['flash']['data'])
            || !array_key_exists('height', $ret['flash']['data'])
        ) {
            throw new Kwf_Exception("_getFlashData() must return an array with keys 'url', 'width' and 'height'");
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
        $ret['flash']['removeHiddenFlash'] = $this->_getSetting('removeHiddenFlash');
        $ret['placeholder'] = $this->_getPlaceholderComponent();

        return $ret;
    }

    protected function _getPlaceholderComponent()
    {
        return $this->getData()->getChildComponent('-placeholder');
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
