<?php
class Kwc_Trl_LinkIntern_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwc_Trl_LinkIntern_Category_PagesModel';
        $ret['generators']['page']['component'] = array(
            'none' => 'Kwc_Basic_None_Component',
            'linkIntern' => 'Kwc_Trl_LinkIntern_LinkTagIntern_Component',
        );
        return $ret;
    }
}
