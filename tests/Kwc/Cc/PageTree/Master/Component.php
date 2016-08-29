<?php
class Kwc_Cc_PageTree_Master_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
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
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'mainMenu' => 'Kwc_Cc_PageTree_Master_MainMenu_Component',
                'bottomMenu' => 'Kwc_Cc_PageTree_Master_BottomMenu_Component'
            ),
            'inherit' => true
        );
        $ret['flags']['chainedType'] = 'Cc';
        return $ret;
    }
}
