<?php
class Kwc_TextImage_ImageEnlarge_LinkTag_TestComponent extends Kwc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_TextImage_ImageEnlarge_LinkTag_TestModel';
        $ret['generators']['child']['component'] = array(
            'none' => 'Kwc_Basic_LinkTag_Empty_Component',
            'download' => 'Kwc_Basic_LinkTag_Empty_Component',
            'enlarge' => 'Kwc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent'
        );
        return $ret;
    }
}
