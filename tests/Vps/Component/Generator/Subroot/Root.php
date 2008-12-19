<?php
class Vps_Component_Generator_Subroot_Root extends Vpc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain']['component'] = array(
            'at' => 'Vps_Component_Generator_Subroot_Domain',
            'ch' => 'Vps_Component_Generator_Subroot_DomainCh'
        );
        $ret['generators']['domain']['model'] = new Vps_Model_FnF(
            array('data' => array(
                array('id' => 'at', 'name' => 'Österreich', 'domain' => 'rotary.at', 'component' => 'at'),
                array('id' => 'ch', 'name' => 'Liechtenstein und Schweiz', 'domain' => 'rotary.ch', 'component' => 'ch')
            ))
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>