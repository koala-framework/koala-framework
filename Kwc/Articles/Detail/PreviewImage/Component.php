<?php
class Kwc_Articles_Detail_PreviewImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['imageLabel'] = trlKwfStatic('Thumbnail');
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwfStatic('default'),
                'width' => 150,
                'height' => 0,
                'cover' => true,
            )
        );
        $ret['generators']['child']['component']['mailImage'] = 'Kwc_Articles_Detail_PreviewImage_Mail_Component';
        return $ret;
    }
}
