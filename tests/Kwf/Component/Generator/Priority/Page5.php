<?php
class Kwf_Component_Generator_Priority_Page5 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box5'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'box' => 'foo'
        );
        return $ret;
    }

}
