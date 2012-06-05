<?php
class Kwc_Cc_PageTree_Master_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Kwc_Root_CategoryGenerator',
            'component' => 'Kwc_Cc_PageTree_Master_Category_Component',
            'model' => new Kwf_Model_FnF(array(
                'data' => array(
                    array('id' => 'main', 'name' => 'main'),
                    array('id' => 'bottom', 'name' => 'bottom'),
                )
            ))
        );
        $ret['flags']['chainedType'] = 'Cc';
        return $ret;
    }
}
