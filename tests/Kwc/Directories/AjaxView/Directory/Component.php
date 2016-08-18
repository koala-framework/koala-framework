<?php
class Kwc_Directories_AjaxView_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Directories_AjaxView_Directory_Model';
        $ret['generators']['detail']['component'] = 'Kwc_Directories_AjaxView_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_AjaxView_View_Component';
        $ret['generators']['categories'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Static',
            'component' => 'Kwc_Directories_AjaxView_Category_Directory_Component',
            'name' => 'categories',
            'showInMenu' => true
        );
        $ret['contentSender'] = 'Kwc_Directories_List_ViewAjax_DirectoryContentSender';
        return $ret;
    }
}
