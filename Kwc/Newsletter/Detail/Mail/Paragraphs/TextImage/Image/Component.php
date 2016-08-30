<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_TextImage_Image_Component
    extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['linkTag'] =
            'Kwc_Newsletter_Detail_Mail_Paragraphs_TextImage_Image_LinkTag_Component';
        $ret['imageCaption'] = false;
        return $ret;
    }
}
