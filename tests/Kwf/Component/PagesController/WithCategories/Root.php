<?php
class Kwf_Component_PagesController_WithCategories_Root extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_PagesController_WithCategories_Category_Component';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>'main', 'name'=>'Main'),
            )
        ));
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
