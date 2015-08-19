<?php
class Kwc_Basic_LinkTagIntern_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Basic_LinkTagIntern_PagesModel';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'link' => 'Kwc_Basic_LinkTagIntern_TestComponent',
            'empty' => 'Kwc_Basic_None_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
