<?php
class Kwc_Trl_Pages_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['component'] = array(
            'test' => 'Kwc_Trl_Pages_TestComponent_Component',
        );
        $ret['generators']['page']['model'] = 'Kwc_Trl_Pages_Category_PagesTestModel';
        return $ret;
    }
}
