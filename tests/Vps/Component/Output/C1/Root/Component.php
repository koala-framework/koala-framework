<?php
class Vps_Component_Output_C1_Root_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Vps_Component_Output_C1_Child_Component'
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vps_Component_Output_C1_Box_Component',
            'inherit' => true,
            'priority' => 0
        );
        
        return $ret;
    }
}
?>