<?php
class Vps_Component_Generator_Subroot_Domain extends Vpc_Root_DomainRoot_Domain_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Vps_Component_Generator_Subroot_Category';
        $ret['generators']['category']['model'] = new Vps_Model_FnF(
            array('data' => array(
                array('id' => 'main', 'name' => 'Hauptmenü'),
                array('id' => 'bottom', 'name' => 'Unten')
            ))
        );

        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Subroot_Static',
            'name' => 'Static'
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>