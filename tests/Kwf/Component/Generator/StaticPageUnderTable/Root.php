<?php
class Kwf_Component_Generator_StaticPageUnderTable_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_StaticPageUnderTable_Page1_Component',
            'name' => 'page1',
            'showInMenu' => true,
            'addUrlPart' => false
        );
        return $ret;
    }
}
