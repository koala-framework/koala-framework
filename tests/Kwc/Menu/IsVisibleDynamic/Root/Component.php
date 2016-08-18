<?php
class Kwc_Menu_IsVisibleDynamic_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwc_Menu_IsVisibleDynamic_Root_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'test' => 'Kwc_Menu_IsVisibleDynamic_Test_Component',
        );
        $ret['flags']['menuCategory'] = 'root';
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Kwc_Menu_IsVisibleDynamic_Root_Menu_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
