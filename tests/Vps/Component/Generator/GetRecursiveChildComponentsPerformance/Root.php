<?php
class Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);

        $ret['generators']['table1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'table1',
            'component' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table',
        );
        $ret['generators']['table2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'table2',
            'component' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table',
        );
        return $ret;
    }
}
