<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Mail');
        $ret['generators']['paragraphs']['component'] = array();
        $ret['generators']['paragraphs']['component']['textImage'] =
            'Kwc_Newsletter_Detail_Mail_Paragraphs_TextImage_Component';

        $cc = Kwf_Registry::get('config')->kwc->childComponents;
        if (isset($cc->Kwc_Newsletter_Detail_Mail_Paragraphs_Component)) {
            $ret['generators']['paragraphs']['component'] = array_merge(
                $ret['generators']['paragraphs']['component'],
                $cc->Kwc_Newsletter_Detail_Mail_Paragraphs_Component->toArray()
            );
        }

        return $ret;
    }
}
