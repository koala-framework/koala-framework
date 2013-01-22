<?php
class Kwc_Articles_Directory_TagSuggestionsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Articles_Directory_TagSuggestionsModel';
    protected $_buttons = array('save');
    protected $_paging = 25;

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('article_title', trl('Artikel'), 300));
        $this->_columns->add(new Kwf_Grid_Column('tag_name', trl('Tag'), 200));
        $this->_columns->add(new Kwf_Grid_Column('user_email', trl('Benutzer'), 200));
        $this->_columns->add(new Kwf_Grid_Column('tag_count_used', trl('Verwendet'), 50));

        $this->_columns->add(new Kwf_Grid_Column_Checkbox('deny', trl('IGNOR'), 50))
            ->setEditor(new Kwf_Form_Field_Checkbox())
            ->setData(new Kwc_Articles_Directory_TagSuggestionsDenyData());
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('accept', trl('OK'), 50))
            ->setData(new Kwc_Articles_Directory_TagSuggestionsAcceptData())
            ->setEditor(new Kwf_Form_Field_Checkbox());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('status', 'new');
        return $ret;
    }
}
