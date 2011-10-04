<?php
class Vps_Component_PagesController_WithCategories_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Vps_Component_PagesController_WithCategories_Category_Component';
        $ret['generators']['category']['model'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>'main', 'name'=>'Main'),
            )
        ));
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
