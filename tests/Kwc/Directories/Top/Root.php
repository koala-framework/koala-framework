<?php
class Kwc_Directories_Top_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['directory'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Directories_Top_Directory'
        );
        $ret['generators']['top'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Directories_Top_Top'
        );
        
        return $ret;
    }

}
