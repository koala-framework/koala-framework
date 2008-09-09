<?php
abstract class Vpc_Box_Search_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['ajax'] = array(
            'class'     => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Box_Search_Ajax_Component',
            'name'      => 'Ajax'
        );

        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'ExtUpdateManager';
        $ret['assets']['dep'][] = 'VpsClearOnFocus';
        $ret['assets']['files'][] = 'vps/Vpc/Box/Search/Component.js';

        $ret['placeholder']['searchButton'] = trlVps('Search');
        $ret['placeholder']['clearOnFocus'] = trlVps('Search term');
        $ret['placeholder']['initialResultText'] = trlVps('Please type at least two characters.');

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['ajaxUrl'] = $this->getData()->getChildComponent('_ajax')->url;
        $ret['searchPageUrl'] = $this->_getSearchPageUrl();
        return $ret;
    }

    abstract public function getSearchComponents();
    abstract protected function _getSearchPageUrl();
}
