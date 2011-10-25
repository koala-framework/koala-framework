<?php
class Kwc_Advanced_Amazon_Nodes_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['amazon'] = array(
            'component' => 'Kwc_Advanced_Amazon_Nodes_TestComponent',
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'amazon'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
