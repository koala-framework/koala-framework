<?php
class Vps_Component_Generator_Plugin_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Plugin_Static',
            'name' => 'Static'
        );
        return $ret;
    }
}
?>