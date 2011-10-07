<?php
class Kwf_Acl_Kwc_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'test' => 'Kwf_Acl_Kwc_TestComponent'
        );
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_Empty_Component'
        );
        $ret['generators']['box']['component'] = array();
        unset($ret['generators']['title']);
        return $ret;
    }
}
