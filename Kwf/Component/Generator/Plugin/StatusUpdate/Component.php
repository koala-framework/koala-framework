<?php
class Kwf_Component_Generator_Plugin_StatusUpdate_Component extends Kwf_Component_Generator_Plugin_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Social Networks');
        $ret['extConfig'] = 'Kwf_Component_Generator_Plugin_StatusUpdate_ExtConfig';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwf/Component/Generator/Plugin/StatusUpdate/Panel.js';
        $ret['backends'] = array(
            'twitter' => 'Kwf_Component_Generator_Plugin_StatusUpdate_Backend_Twitter'
        );
        return $ret;
    }
}
