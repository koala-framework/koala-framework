<?php
class Kwf_Component_Generator_Subroot_Domain extends Kwc_Root_DomainRoot_Domain_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_Generator_Subroot_Category';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(
            array('data' => array(
                array('id' => 'main', 'name' => 'Hauptmenü'),
                array('id' => 'bottom', 'name' => 'Unten')
            ))
        );

        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_Subroot_Static',
            'name' => 'Static'
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>