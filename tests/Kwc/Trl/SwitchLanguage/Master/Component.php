<?php
class Kwc_Trl_SwitchLanguage_Master_Component extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators'] = array();
        $ret['generators']['category'] = array(
            'class' => 'Kwc_Root_CategoryGenerator',
            'component' => 'Kwc_Trl_SwitchLanguage_Category_Component',
            'model' => new Kwf_Model_FnF(array(
                'data' => array(
                    array('id' => 'main', 'name' => 'main')
                )
            ))
        );
        $ret['generators']['switchLanguage'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Box_SwitchLanguage_Component',
            'inherit' => true
        );
        return $ret;
    }
}
