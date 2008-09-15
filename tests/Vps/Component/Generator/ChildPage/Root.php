<?php
class Vps_Component_Generator_ChildPage_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_ChildPage_Child'
        );
        $ret['generators']['box']['component'] = array('form' => 'Vpc_Form_Component');
        unset($ret['generators']['title']);
        return $ret;
    }
}
?>