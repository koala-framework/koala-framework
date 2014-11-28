<?php
class Kwf_Assets_DependencyWithComponents_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Assets_DependencyWithComponents_TestComponent_Component',
        );
        return $ret;
    }
}
