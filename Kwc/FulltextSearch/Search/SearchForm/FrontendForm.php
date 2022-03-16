<?php
class Kwc_FulltextSearch_Search_SearchForm_FrontendForm extends Kwf_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->_model = new Kwf_Model_FnF();
        $this->fields->add(new Kwf_Form_Field_TextField('query', 'Query'))
            ->setAutoComplete(false)
            ->setHideLabel(true)
            ->setEmptyText(trlKwfStatic('Search'))
            ->setNamePrefix(''); // to get "default" parameter "search" which is automatically excluded on user tracking systems
    }
}
