<?php
class Kwc_Cc_Composite_Master_Composite_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = array(
            'test1' => 'Kwc_Cc_Composite_Master_Composite_Test_Component',
            'test2' => 'Kwc_Cc_Composite_Master_Composite_Test_Component',
        );
        return $ret;
    }
}
