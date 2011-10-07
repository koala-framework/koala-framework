<?php
class Kwf_Assets_WithComponents_Root_Component extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['foo']['class'] = 'Kwf_Component_Generator_Static';
        $ret['generators']['foo']['component'] = 'Kwf_Assets_WithComponents_Component1_Component';
        return $ret;
    }
}
