<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component'] = array();
        $ret['generators']['paragraphs']['component']['textImage'] =
            'Vpc_Newsletter_Detail_Mail_Paragraphs_TextImage_Component';

        $cc = Vps_Registry::get('config')->vpc->childComponents;
        if (isset($cc->Vpc_Newsletter_Detail_Mail_Paragraphs_Component)) {
            $ret['generators']['paragraphs']['component'] = array_merge(
                $ret['generators']['paragraphs']['component'],
                $cc->Vpc_Newsletter_Detail_Mail_Paragraphs_Component->toArray()
            );
        }

        return $ret;
    }
}
