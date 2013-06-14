<?php
class Kwc_IncludeCode_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_IncludeCode_Page1_Component',
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_IncludeCode_Page2_Component',
        );
        $ret['generators']['page3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_IncludeCode_Page3_Component',
        );
        $ret['generators']['page4'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_IncludeCode_Page4_Component',
        );
        return $ret;
    }
}
