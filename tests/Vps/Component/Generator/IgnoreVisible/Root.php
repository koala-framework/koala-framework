<?php
class Vps_Component_Generator_IgnoreVisible_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_IgnoreVisible_Child'
        );
        return $ret;
    }
}
?>