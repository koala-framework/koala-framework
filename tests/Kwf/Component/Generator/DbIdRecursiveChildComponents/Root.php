<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Page',
        );
        return $ret;
    }
}
