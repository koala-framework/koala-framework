<?php
class Kwf_Component_ChildSettings_TwoLevelsSingleStatic_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['first'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_ChildSettings_TwoLevelsSingleStatic_First'
        );

        $ret['childSettings']['first.second'] = array(
            'componentName' => 'test123'
        );

        return $ret;
    }
}
