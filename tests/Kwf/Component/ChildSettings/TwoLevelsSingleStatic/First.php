<?php
class Kwf_Component_ChildSettings_TwoLevelsSingleStatic_First extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['second'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_None_Component'
        );
        return $ret;
    }
}