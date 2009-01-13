<?php
class Vps_Component_Generator_Domain_Root extends Vpc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain']['component'] = array(
            'at' => 'Vps_Component_Generator_Domain_Domain',
            'ch' => 'Vps_Component_Generator_Domain_DomainCh'
        );
        $ret['generators']['domain']['model'] = 'Vps_Component_Generator_Domain_Model';
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>