<?php
class Kwc_Articles_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Kwc_Articles_Detail_Paragraphs_Component';
        $ret['generators']['child']['component']['previewImage'] = 'Kwc_Articles_Detail_PreviewImage_Component';
        $ret['generators']['child']['component']['questionsAnswers'] = 'Kwc_Articles_Detail_QuestionsAnswers_Component';
        $ret['generators']['child']['component']['tags'] = 'Kwc_Articles_Detail_Tags_Component';
        $ret['generators']['child']['component']['favor'] = 'Kwc_Articles_Detail_Favor_Component';

        $ret['generators']['feedback'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Articles_Detail_Feedback_Component',
            'name' => trlKwf('Feedback')
        );

        $ret['flags']['processInput'] = true;

        $ret['editComponents'] = array('content', 'questionsAnswers', 'feedback');
        return $ret;
    }

    public function processInput($input)
    {
        $this->getData()->row->markRead();
    }
}
