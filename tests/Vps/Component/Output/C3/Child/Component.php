<?php
class Vps_Component_Output_C3_Child_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array('Vps_Component_Output_Plugin_Plugin');
        return $ret;
    }

    public function hasContent()
    {
        return true;
    }
}
?>