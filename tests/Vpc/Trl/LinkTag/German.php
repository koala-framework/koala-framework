<?php
class Vpc_Trl_LinkTag_German extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_LinkTag_LinkTag_Component',
            'name' => 'test1',
        );
        $ret['generators']['test2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_LinkTag_LinkTag_Component',
            'name' => 'test2',
        );
        $ret['generators']['test3'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_LinkTag_LinkTag_Component',
            'name' => 'test3',
        );
        return $ret;
    }
}
