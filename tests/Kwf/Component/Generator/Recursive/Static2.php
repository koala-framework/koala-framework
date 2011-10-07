<?php
class Vps_Component_Generator_Recursive_Static2 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['flag'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Recursive_Flag'
        );
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'Foo'
        );
        return $ret;
    }

}
