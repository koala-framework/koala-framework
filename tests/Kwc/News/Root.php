<?php
class Kwc_News_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_News_PagesModel';
        $ret['generators']['page']['component'] = array(
            'news' => 'Kwc_News_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
