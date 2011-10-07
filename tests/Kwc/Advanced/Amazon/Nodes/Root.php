<?php
class Vpc_Advanced_Amazon_Nodes_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['amazon'] = array(
            'component' => 'Vpc_Advanced_Amazon_Nodes_TestComponent',
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'amazon'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
