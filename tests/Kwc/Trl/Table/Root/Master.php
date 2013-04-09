<?php
class Kwc_Trl_Table_Root_Master extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Table_Table_Component',
            'name' => 'table',
        );
        unset($ret['generators']['flag']);
        unset($ret['generators']['box']);
        unset($ret['generators']['category']);
        return $ret;
    }
}
