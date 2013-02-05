<?php
class Kwc_Trl_MenuCache_Master extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['category'] = array(
            'class' => 'Kwc_Root_CategoryGenerator',
            'component' => 'Kwc_Trl_MenuCache_Category_Component',
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
                'mainMenu' => 'Kwc_Trl_MenuCache_MainMenu_Component'
            ),
            'inherit' => true
        );
        return $ret;
    }
}
