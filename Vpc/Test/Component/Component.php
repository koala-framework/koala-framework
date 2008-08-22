<?php
class Vpc_Test_Component_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        $ret['generators']['multiBox'] = array(
            'class' => 'Vps_Component_Generator_MultiBox_Static',
            'component' => 'Vpc_Test_Component_Flag_Component'
        );
        $ret['generators']['pageStatic'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Test_Component_Flag_Component',
            'unique' => true
        );
        $ret['generators']['pageTable'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'model' => 'Vps_Model_FnF',
            'selectClass' => 'Vps_Test_Db_Table_Select_Generator'
        );
        $ret['generators']['pseudoPageTable'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'model' => 'Vps_Model_FnF',
            'selectClass' => 'Vps_Test_Db_Table_Select_Generator',
            'inherit' => true
        );
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'unique' => true,
            'inherit' => true
        );
        $ret['generators']['table'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'model' => 'Vps_Model_FnF',
            'selectClass' => 'Vps_Test_Db_Table_Select_Generator'
        );
        $ret['plugins'] = array(
            'Vpc_Test_Component_Plugin_Component'
        );
        $ret['editComponents'] = array('box', 'static');
        return $ret;
    }
}
?>