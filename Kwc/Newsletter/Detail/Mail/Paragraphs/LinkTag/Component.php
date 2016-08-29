<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Component
    extends Kwc_Basic_LinkTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['unsubscribe'] =
            'Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component';
        $ret['generators']['child']['component']['editSubscriber'] =
            'Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_EditSubscriber_Component';
        return $ret;
    }
}