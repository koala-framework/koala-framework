<?php
class Vpc_Box_Title_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_Box_Title_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
            'table' => 'Vpc_Box_Title_Table',
        );
        unset($ret['generators']['box']);
        return $ret;
    }

}
