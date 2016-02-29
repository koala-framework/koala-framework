<?php
class Kwf_Component_RootPlugin_MaskComponentLink_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_RootPlugin_MaskComponentLink_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['flags']['menuCategory'] = 'root';
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Kwf_Component_RootPlugin_MaskComponentLink_Menu_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
