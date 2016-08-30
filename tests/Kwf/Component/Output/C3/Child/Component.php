<?php
class Kwf_Component_Output_C3_Child_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['plugins'] = array('Kwf_Component_Output_Plugin_Plugin');
        return $ret;
    }

    public function hasContent()
    {
        return true;
    }
}
?>