<?php
class Kwc_FormCache_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form',
            'component' => 'Kwc_FormCache_Form_Component'
        );
        return $ret;
    }
}