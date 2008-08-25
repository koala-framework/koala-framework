<?php
class Vps_Component_Generator_Components_Recursive extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Components_RecursiveStatic',
        );
        $ret['generators']['table'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => array(
                'empty' => 'Vpc_Basic_Empty_Component',
                'recursive' => 'Vps_Component_Generator_Components_RecursiveTable',
            ),
            'model' => 'Vps_Model_FnF',
            'selectClass' => 'Vps_Db_Table_Select_TestGenerator'
        );
        return $ret;
    }
}
?>