<?php
class Vpc_Paragraphs_Paragraphs extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Paragraphs_ParagraphsModel';
        $ret['generators']['paragraphs']['component'] = array(
            'paragraph' => 'Vpc_Paragraphs_Paragraph'
        );
        return $ret;
    }
}
