<?php
class Vpc_Cc_Composite_Master_Composite_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = array(
            'test1' => 'Vpc_Cc_Composite_Master_Composite_Test_Component',
            'test2' => 'Vpc_Cc_Composite_Master_Composite_Test_Component',
        );
        return $ret;
    }
}
