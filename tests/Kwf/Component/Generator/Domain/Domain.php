<?php
class Vps_Component_Generator_Domain_Domain extends Vpc_Root_DomainRoot_Domain_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Vps_Component_Generator_Domain_Category';
        $ret['generators']['category']['model'] = new Vps_Model_FnF(
            array('data' => array(
                array('id' => 'main', 'name' => 'Hauptmenü'),
                array('id' => 'bottom', 'name' => 'Unten')
            ))
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>