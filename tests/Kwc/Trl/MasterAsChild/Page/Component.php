<?php
class Kwc_Trl_MasterAsChild_Page_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['subpage'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'name' => 'subpage',
        );
        return $ret;
    }
}
