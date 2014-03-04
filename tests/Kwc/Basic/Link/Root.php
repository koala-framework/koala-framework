<?php
class Kwc_Basic_Link_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Basic_Link_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'link' => 'Kwc_Basic_Link_Link_Component'
        );
        return $ret;
    }
}
