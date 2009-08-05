<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component']['textImage'] =
            'Vpc_Newsletter_Detail_Mail_Paragraphs_TextImage_Component';
        return $ret;
    }
}
