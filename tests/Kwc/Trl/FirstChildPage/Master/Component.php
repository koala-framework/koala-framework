<?php
class Kwc_Trl_FirstChildPage_Master_Component extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['testLink'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_FirstChildPage_TestLink_Component',
        );

        $ret['generators']['cat1'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Trl_FirstChildPage_Category_Component',
        );

        unset($ret['generators']['flag']);
        unset($ret['generators']['box']);
        unset($ret['generators']['category']);
        $ret['editComponents'] = array();
        return $ret;
    }
}
