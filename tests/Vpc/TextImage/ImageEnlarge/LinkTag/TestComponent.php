<?php
class Vpc_TextImage_ImageEnlarge_LinkTag_TestComponent extends Vpc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_TextImage_ImageEnlarge_LinkTag_TestModel';
        $ret['generators']['child']['component'] = array(
            'none' => 'Vpc_Basic_LinkTag_Empty_Component',
            'download' => 'Vpc_Basic_LinkTag_Empty_Component',
            'enlarge' => 'Vpc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent'
        );
        return $ret;
    }
}
