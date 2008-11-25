<?php
class Vpc_Directories_Top_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['directory'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Directories_Top_Directory'
        );
        $ret['generators']['top'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Directories_Top_Top'
        );
        
        return $ret;
    }

}
