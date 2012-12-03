<?php
class Kwf_Component_Generator_StaticPageUnderStatic_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['component1'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_StaticPageUnderStatic_C1_Component'
        );
        $ret['generators']['component2'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_StaticPageUnderStatic_C2_Component',
            'addUrlPart' => false
        );
        return $ret;
    }
}
