<?php
class Kwc_Trl_FormDynamic_Master extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_FormDynamic_Form_Component',
            'name' => 'test1',
        );
        return $ret;
    }
}
