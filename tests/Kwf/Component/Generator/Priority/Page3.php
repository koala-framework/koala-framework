<?php
class Kwf_Component_Generator_Priority_Page3 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box4'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_None_Component',
            'inherit' => false,
            'box' => 'foo'
        );
        $ret['generators']['page4'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_None_Component',
            'name' => 'page4'
        );
        $ret['generators']['page5'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_Priority_Page5',
            'name' => 'page5'
        );
        return $ret;
    }

}
