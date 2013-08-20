<?php
class Kwc_Trl_MasterAsChild_Master extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_MasterAsChild_Page_Component',
            'name' => 'page',
        );
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'inherit' => true,
            'unique' => false
        );
        return $ret;
    }
}
