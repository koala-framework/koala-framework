<?php
class Kwf_Component_Output_C2_Root_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Kwf_Component_Output_C2_Child_Component',
            'childNoCache' => 'Kwf_Component_Output_C2_ChildNoCache_Component'
        );
        $ret['contentWidth'] = 600;
        return $ret;
    }
}
?>