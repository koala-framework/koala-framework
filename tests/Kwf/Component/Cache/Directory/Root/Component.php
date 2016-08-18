<?php
class Kwf_Component_Cache_Directory_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['dir'] = array(
            'component' => 'Kwf_Component_Cache_Directory_Root_Directory_Component',
            'class' => 'Kwf_Component_Generator_Page_Static'
        );
        $ret['generators']['list'] = array(
            'component' => 'Kwf_Component_Cache_Directory_Root_List_Component',
            'class' => 'Kwf_Component_Generator_Page_Static'
        );
        $ret['generators']['trldir'] = array(
            'component' => 'Kwf_Component_Cache_Directory_Root_Directory_Trl_Component.Kwf_Component_Cache_Directory_Root_Directory_Component',
            'class' => 'Kwf_Component_Cache_Directory_Root_TrlGenerator'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
