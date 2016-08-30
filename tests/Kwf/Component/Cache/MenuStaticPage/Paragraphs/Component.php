<?php
class Kwf_Component_Cache_MenuStaticPage_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwf_Component_Cache_MenuStaticPage_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'test' => 'Kwf_Component_Cache_MenuStaticPage_Test_Component',
        );
        return $ret;
    }
}
