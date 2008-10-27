<?php
class Vps_Component_Output_Root2_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Vps_Component_Output_Child_Component',
            'childNoCache' => 'Vps_Component_Output_ChildNoCache_Component'
        );
        
        return $ret;
    }
}
?>