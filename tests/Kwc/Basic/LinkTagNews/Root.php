<?php
class Kwc_Basic_LinkTagNews_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Basic_LinkTagNews_PagesModel';
        $ret['generators']['page']['component'] = array(
            'news' => 'Kwc_News_TestComponent',
            'link' => 'Kwc_Basic_LinkTagNews_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
