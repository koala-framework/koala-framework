<?php
class Vps_Component_Generator_Components_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Model_TestPages';
        $ret['generators']['box']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Components_Multiple'
        );
        return $ret;
    }
}
?>