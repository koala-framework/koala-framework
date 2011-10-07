<?php
class Kwc_Box_InheritContent_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['title']);
        $ret['generators']['box']['component'] = array(
            'ic' => 'Kwc_Box_InheritContent_InheritContent'
        );
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Box_InheritContent_Page1',
            'name' => 'page1'
        );

        $ret['editComponents'] = array('ic');

        return $ret;
    }

}
