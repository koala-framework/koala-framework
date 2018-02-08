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
        $ret['generators']['child']['component']['activation'] =
            'Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Activation_Component';

        $cc = Kwf_Registry::get('config')->kwc->childComponents;
        if (isset($cc->Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Component)) {
            $ret['generators']['child']['component'] = array_merge(
                $ret['generators']['child']['component'],
                $cc->Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Component->toArray()
            );
        }
        return $ret;
    }
}
