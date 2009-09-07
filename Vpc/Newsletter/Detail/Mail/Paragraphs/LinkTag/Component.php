<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Component
    extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['link']['component']['unsubscribe'] =
            'Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component';
        $ret['generators']['link']['component']['editSubscriber'] =
            'Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_EditSubscriber_Component';
        return $ret;
    }
}