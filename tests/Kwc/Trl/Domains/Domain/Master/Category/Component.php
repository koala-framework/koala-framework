<?php
class Kwc_Trl_Domains_Domain_Master_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['component'] = array(
            'test' => 'Kwc_Trl_Domains_TestComponent_Component',
        );
        $ret['generators']['page']['model'] = 'Kwc_Trl_Domains_Domain_Master_Category_PagesTestModel';
        return $ret;
    }
}
