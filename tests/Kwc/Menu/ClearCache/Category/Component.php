<?php
class Kwc_Menu_ClearCache_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['generators']['page']['model'] = 'Kwc_Menu_ClearCache_Category_PagesModel';
        return $ret;
    }
}
