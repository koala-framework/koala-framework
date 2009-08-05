<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_TextImage_Text_Component
    extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['link'] =
            'Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Component';
        return $ret;
    }
}
