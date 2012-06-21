<?php
class Kwf_Component_ExpandedComponentId_Root extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_ExpandedComponentId_Category';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>'main', 'name'=>'main'),
            array('id'=>'bottom', 'name'=>'bottom'),
        )));
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>