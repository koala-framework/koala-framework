<?php
class Vpc_Flash_Root_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['flash'] = array(
            'component' => 'Vpc_Flash_Component',
            'class' => 'Vps_Component_Generator_Page_Static',
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
