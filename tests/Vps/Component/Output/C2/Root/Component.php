<?php
class Vps_Component_Output_C2_Root_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Vps_Component_Output_C2_Child_Component',
            'childNoCache' => 'Vps_Component_Output_C2_ChildNoCache_Component'
        );
        
        return $ret;
    }
}
?>