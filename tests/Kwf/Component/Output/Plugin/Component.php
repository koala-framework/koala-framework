<?php
class Kwf_Component_Output_Plugin_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = array(
            'child' => 'Kwf_Component_Output_C1_Child_Component'
        );
        $ret['plugins'] = array('Kwf_Component_Output_Plugin_Plugin_Component');
        $ret['contentWidth'] = 600;
        return $ret;
    }
}
?>