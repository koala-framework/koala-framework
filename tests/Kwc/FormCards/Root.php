<?php
class Kwc_FormCards_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form',
            'component' => 'Kwc_FormCards_Form_Component'
        );
        $ret['generators']['formradio'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_FormCards_FormRadio_Component'
        );
        return $ret;
    }
}
