<?php
class Kwc_FormDynamic_Basic_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form',
            'component' => 'Kwc_FormDynamic_Basic_Form_Component'
        );
        $ret['generators']['form2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form2',
            'component' => 'Kwc_FormDynamic_Basic_Form_Component'
        );
        $ret['generators']['form3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form3',
            'component' => 'Kwc_FormDynamic_Basic_Form_Component'
        );
        return $ret;
    }
}