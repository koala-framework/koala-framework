<?php
class Vps_Component_Generator_Plugin_StatusUpdate_Component extends Vps_Component_Generator_Plugin_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Social Networks');
        $ret['extConfig'] = 'Vps_Component_Generator_Plugin_StatusUpdate_ExtConfig';
        $ret['assetsAdmin']['files'][] = 'vps/Vps/Component/Generator/Plugin/StatusUpdate/Panel.js';
        $ret['backends'] = array(
            'twitter' => 'Vps_Component_Generator_Plugin_StatusUpdate_Backend_Twitter'
        );
        return $ret;
    }
}
