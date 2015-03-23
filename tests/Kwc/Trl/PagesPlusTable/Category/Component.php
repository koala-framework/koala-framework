<?php
class Kwc_Trl_PagesPlusTable_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['component'] = array(
            'test' => 'Kwc_Trl_PagesPlusTable_TestComponent_Component',
            'testTable' => 'Kwc_Trl_PagesPlusTable_TestTableComponent_Component',
        );
        $ret['generators']['page']['model'] = 'Kwc_Trl_PagesPlusTable_Category_PagesTestModel';
        return $ret;
    }
}
