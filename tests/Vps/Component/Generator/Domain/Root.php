<?php
class Vps_Component_Generator_Domain_Root extends Vpc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain']['component'] = 'Vps_Component_Generator_Domain_Domain';
        $ret['generators']['domain']['model'] = new Vps_Model_FnF(
            array('data' => array(
                array('id' => 'at', 'name' => 'Österreich', 'domain' => 'rotary.at'),
                array('id' => 'ch', 'name' => 'Liechtenstein und Schweiz', 'domain' => 'rotary.ch')
            ))
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>