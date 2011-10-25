<?php
class Kwc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestComponent extends Kwc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestModel';
        $ret['generators']['link']['component'] = array(
            'none' => 'Kwc_Basic_LinkTag_Empty_Component',
            'enlarge' => 'Kwc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent'
        );
        return $ret;
    }
}
