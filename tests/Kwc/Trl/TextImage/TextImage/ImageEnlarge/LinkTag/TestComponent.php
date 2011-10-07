<?php
class Vpc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestComponent extends Vpc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestModel';
        $ret['generators']['link']['component'] = array(
            'none' => 'Vpc_Basic_LinkTag_Empty_Component',
            'enlarge' => 'Vpc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent'
        );
        return $ret;
    }
}
