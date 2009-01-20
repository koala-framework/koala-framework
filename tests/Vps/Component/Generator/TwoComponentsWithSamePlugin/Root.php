<?php
class Vps_Component_Generator_TwoComponentsWithSamePlugin_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['static0'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_TwoComponentsWithSamePlugin_Static0',
        );
        return $ret;
    }
}
?>