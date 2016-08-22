<?php
class Kwc_Trl_LinkIntern_Master_Component extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_LinkIntern_LinkTagIntern_Component',
            'name' => 'test1',
        );
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_LinkIntern_LinkTagIntern_Component',
            'name' => 'test2',
        );
        $ret['generators']['test3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_LinkIntern_LinkTagIntern_Component',
            'name' => 'test3',
        );

        $ret['generators']['cat1'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Trl_LinkIntern_Category_Component',
        );

        unset($ret['generators']['flag']);
        unset($ret['generators']['box']);
        unset($ret['generators']['category']);
        $ret['editComponents'] = array();
        return $ret;
    }
}
