<?php
class Kwf_Component_Generator_IgnoreVisible_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_IgnoreVisible_Child'
        );
        return $ret;
    }
}
?>