<?php
class Kwf_Component_Fulltext_BasicHtml_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Fulltext_BasicHtml_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'html' => 'Kwf_Component_Fulltext_BasicHtml_Html_Component',
            'htmlChild' => 'Kwf_Component_Fulltext_BasicHtml_HtmlChild_Component',
        );

        //required so fulltext events will be processed
        $ret['generators']['search'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Fulltext_BasicHtml_FulltextSearch_Component',
        );
        return $ret;
    }
}
