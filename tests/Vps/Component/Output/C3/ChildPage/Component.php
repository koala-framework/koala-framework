<?php
class Vps_Component_Output_C3_ChildPage_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['childpage'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Output_C3_ChildPage2_Component'
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vps_Component_Output_C3_Box_Component',
            'inherit' => true,
            'priority' => 0
        );
        return $ret;
    }
}
?>