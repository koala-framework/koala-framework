<?php
class Kwf_Component_Generator_Unique_Page extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box2'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'priority' => 3,
            'box' => 'box'
        );
        return $ret;
    }

}
