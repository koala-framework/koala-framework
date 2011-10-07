<?php
class Kwf_Component_Generator_Priority_Page2 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box3'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'inherit' => false,
            'box' => 'foo'
        );
        return $ret;
    }

}
