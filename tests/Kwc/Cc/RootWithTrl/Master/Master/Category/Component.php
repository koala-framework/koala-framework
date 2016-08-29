<?php
class Kwc_Cc_RootWithTrl_Master_Master_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwc_Cc_RootWithTrl_Master_Master_Category_PagesModel';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component'
        );
        return $ret;
    }
}