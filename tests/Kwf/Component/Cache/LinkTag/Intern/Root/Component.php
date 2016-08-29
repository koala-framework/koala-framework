<?php
class Kwf_Component_Cache_LinkTag_Intern_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_LinkTag_Intern_Root_Model';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Page_Table',
            'component' => 'Kwf_Component_Cache_LinkTag_Intern_Root_TableItem_Component',
            'model' => 'Kwf_Component_Cache_LinkTag_Intern_Root_TableModel',
            'filenameColumn' => 'filename',
            'dbIdShortcut' => 'table_'
        );
        $ret['generators']['link'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_LinkTag_Intern_Root_Link_Component'
        );
        return $ret;
    }
}
