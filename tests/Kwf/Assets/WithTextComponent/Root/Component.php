<?php
class Kwf_Assets_WithTextComponent_Root_Component extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['foo']['class'] = 'Kwf_Component_Generator_Static';
        $ret['generators']['foo']['component'] = 'Kwc_Basic_Text_Component';
        return $ret;
    }
}
