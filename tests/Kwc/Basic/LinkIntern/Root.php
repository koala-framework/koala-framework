<?php
class Kwc_Basic_LinkIntern_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Basic_LinkIntern_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'linktagintern' => 'Kwc_Basic_LinkIntern_LinkTag_Component'
        );
        return $ret;
    }
}
