<?php
class Kwc_Trl_FirstChildPage_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwc_Trl_FirstChildPage_Category_PagesModel';
        $ret['generators']['page']['component'] = array(
            'none' => 'Kwc_Basic_None_Component',
            'firstChildPage' => 'Kwc_Basic_LinkTag_FirstChildPage_Component',
        );
        return $ret;
    }
}
