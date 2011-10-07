<?php
class Kwf_Component_Generator_GetComponentByClassWithComponentId_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);

        $ret['generators']['foo1'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_GetComponentByClassWithComponentId_Table'
        );
        $ret['generators']['foo2'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_GetComponentByClassWithComponentId_Table2'
        );
        return $ret;
    }
}
