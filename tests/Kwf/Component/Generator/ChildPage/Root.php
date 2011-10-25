<?php
class Kwf_Component_Generator_ChildPage_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_ChildPage_Child'
        );
        $ret['generators']['box']['component'] = array('form' => 'Kwc_Form_Component');
        unset($ret['generators']['title']);
        return $ret;
    }
}
?>