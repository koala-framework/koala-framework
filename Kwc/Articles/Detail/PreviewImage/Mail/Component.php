<?php
class Kwc_Articles_Detail_PreviewImage_Mail_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['useParentImage'] = true;
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwf('default'),
                'width' => 230,
                'height' => 0,
                'bestfit' => false,
            )
        );
        return $ret;
    }
}
