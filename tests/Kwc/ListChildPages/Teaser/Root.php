<?php
class Kwc_ListChildPages_Teaser_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_ListChildPages_Teaser_PageModel';
        $ret['generators']['page']['component'] = array(
            'listchild' => 'Kwc_ListChildPages_Teaser_Teaser_Component',
            'listchildwithvisible' => 'Kwc_ListChildPages_Teaser_TeaserWithChild_Component',
            'empty' => 'Kwc_Basic_None_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
