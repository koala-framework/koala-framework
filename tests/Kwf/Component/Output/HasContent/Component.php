<?php
class Kwf_Component_Output_HasContent_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = array(
            'child' => 'Kwf_Component_Output_C1_ChildChild_Component'
        );
        $ret['contentWidth'] = 600;
        return $ret;
    }
}
?>