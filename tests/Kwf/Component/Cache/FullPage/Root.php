<?php
class Kwf_Component_Cache_FullPage_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_FullPage_Test1_Component',
            'name' => 'test1'
        );
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_FullPage_Test2_Component',
            'name' => 'test2'
        );
        $ret['generators']['test3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_FullPage_Test3_Component',
            'name' => 'test3'
        );
        $ret['generators']['test4'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_FullPage_Test4_Component',
            'name' => 'test4'
        );
        return $ret;
    }
}
