<?php
class Kwc_Directories_AjaxViewMultipleDirectories_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['component'] = array();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['directory'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Directories_AjaxViewMultipleDirectories_Directory_Component'
        );
        $ret['generators']['list'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Directories_AjaxViewMultipleDirectories_List_Component'
        );
        return $ret;
    }
}
