<?php
class Kwf_Component_Cache_MenuStaticPage_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwf_Component_Cache_MenuStaticPage_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'test' => 'Kwf_Component_Cache_MenuStaticPage_Test_Component',
        );
        return $ret;
    }
}
