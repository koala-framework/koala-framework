<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_TextImage_Image_LinkTag_Component
    extends Vpc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['link']['component']['enlarge']);
        return $ret;
    }
}
