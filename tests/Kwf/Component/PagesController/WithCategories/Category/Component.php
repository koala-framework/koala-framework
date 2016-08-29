<?php
class Kwf_Component_PagesController_WithCategories_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwf_Component_PagesController_WithCategories_Category_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component',
        );
        return $ret;
    }
}
