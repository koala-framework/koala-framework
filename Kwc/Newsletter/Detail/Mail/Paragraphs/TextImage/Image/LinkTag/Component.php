<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_TextImage_Image_LinkTag_Component
    extends Kwc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['link']['component']['enlarge']);
        return $ret;
    }
}
