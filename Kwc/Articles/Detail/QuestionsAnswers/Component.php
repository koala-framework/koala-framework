<?php
class Kwc_Articles_Detail_QuestionsAnswers_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Questions and answers');
        $ret['generators']['child']['component'] = 'Kwc_Articles_Detail_QuestionsAnswers_SwitchDisplay_Component';
        return $ret;
    }
}
