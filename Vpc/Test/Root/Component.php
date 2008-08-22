<?php
class Vpc_Test_Root_Component extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Test_Model_Pages';
        $ret['generators']['box']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Test_Component_Component'
        );
        return $ret;
    }
}
?>