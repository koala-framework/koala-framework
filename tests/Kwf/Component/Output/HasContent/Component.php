<?php
class Vps_Component_Output_HasContent_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Vps_Component_Output_C1_ChildChild_Component'
        );
        return $ret;
    }
}
?>