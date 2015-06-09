<?php
class Kwc_Articles_Detail_QuestionsAnswers_SwitchDisplay_Component extends Kwc_Composite_SwitchDisplay_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childSettings'] = array(
            'child_linktext' => array(
                'componentName' => trlKwfStatic('Question')
            ),
            'child_content' => array(
                'componentName' => trlKwfStatic('Answer')
            )
        );
        return $ret;
    }
}
