<?php
class Kwf_Component_ChildSettings_SingleStatic_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['empty'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_None_Component'
        );

        $ret['childSettings']['empty'] = array(
            'componentName' => 'test123'
        );
        return $ret;
    }
}
