<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);

        $ret['generators']['table1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'table1',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table',
        );
        $ret['generators']['table2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'table2',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table',
        );
        return $ret;
    }
}
