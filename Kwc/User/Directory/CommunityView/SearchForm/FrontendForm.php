<?php
class Kwc_User_Directory_CommunityView_SearchForm_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        $this->setModel(new Kwf_Model_FnF());
        $this->add(new Kwf_Form_Field_TextField('query', trlKwfStatic('Name')));
        parent::_init();
    }
}
