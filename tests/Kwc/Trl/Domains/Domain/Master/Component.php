<?php
class Kwc_Trl_Domains_Domain_Master_Component extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Kwc_Root_CategoryGenerator',
            'component' => 'Kwc_Trl_Domains_Domain_Master_Category_Component',
            'model' => new Kwf_Model_FnF(array(
                'data' => array(
                    array('id' => 'main', 'name' => 'main')
                )
            ))
        );
        return $ret;
    }
}
