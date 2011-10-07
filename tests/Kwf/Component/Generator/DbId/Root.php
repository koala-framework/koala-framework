<?php
class Kwf_Component_Generator_DbId_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array('empty' => 'Kwc_Basic_Empty_Component');
        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_DbId_Static',
            'name' => 'Static'
        );
        return $ret;
    }
}
?>