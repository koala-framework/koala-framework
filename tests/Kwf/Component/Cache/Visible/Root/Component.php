<?php
class Kwf_Component_Cache_Visible_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwf_Component_Cache_Visible_Root_Child',
            'model' => 'Kwf_Component_Cache_Visible_Root_DirectoryModel'
        );
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_Visible_Root_PagesModel';
        $ret['generators']['page']['component'] = array(
            'child' => 'Kwf_Component_Cache_Visible_Root_Child'
        );
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
