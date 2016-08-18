<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_TextImage_Text_Component
    extends Kwc_Basic_Text_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['link'] =
            'Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Component';
        return $ret;
    }
}
