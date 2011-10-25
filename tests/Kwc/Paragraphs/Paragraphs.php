<?php
class Kwc_Paragraphs_Paragraphs extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Paragraphs_ParagraphsModel';
        $ret['generators']['paragraphs']['component'] = array(
            'paragraph' => 'Kwc_Paragraphs_Paragraph'
        );
        return $ret;
    }

    public function getContentWidth()
    {
        return 400;
    }
}
