<?php
class Vps_Component_Output_Root2 extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Vps_Component_Output_Child',
            'childNoCache' => 'Vps_Component_Output_ChildNoCache'
        );
        
        return $ret;
    }
}
?>