<?php
class Kwf_Component_Output_HasContent_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'child' => 'Kwf_Component_Output_C1_ChildChild_Component'
        );
        $ret['contentWidth'] = 600;
        return $ret;
    }
}
?>