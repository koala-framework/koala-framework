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

        $ret['flags']['hasFulltext'] = true;
        $ret['flags']['processInput'] = true;

        $ret['editComponents'] = array('content', 'questionsAnswers', 'feedback');

        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        $ret['assetsAdmin']['dep'][] = 'KwfFormSuperBoxSelect';
        return $ret;
    }

    public function processInput($input)
    {
        $this->getData()->row->markRead();
    }

    public function getFulltextContent()
    {
        $ret = array();
        $ret['type'] = 'kwc_article';
        $ret['created'] = new Kwf_DateTime($this->getData()->row->date);
        $ret['only_intern'] = (bool)$this->getData()->row->only_intern;
        return $ret;
    }

    public static function modifyItemData(Kwf_Component_Data $item)
    {
        $item->categories = array();
        foreach ($item->row->getChildRows('Categories') as $category) {
            $item->categories[] = $category->getParentRow('Category')->name;
        }
    }
}
