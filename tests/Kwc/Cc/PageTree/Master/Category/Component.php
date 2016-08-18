<?php
class Kwc_Cc_PageTree_Master_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwc_Cc_PageTree_Master_Category_PagesModel';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'none' => 'Kwc_Basic_None_Component',
            'test' => 'Kwc_Cc_PageTree_Master_Category_Test_Component',
        );
        return $ret;
    }
}
