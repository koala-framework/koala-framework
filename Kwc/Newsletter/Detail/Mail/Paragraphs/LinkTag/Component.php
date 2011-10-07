<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Component
    extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['unsubscribe'] =
            'Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component';
        $ret['generators']['child']['component']['editSubscriber'] =
            'Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_EditSubscriber_Component';
        return $ret;
    }
}