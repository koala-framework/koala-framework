<?php
class Vpc_Forum_Search_View_SearchForm_FrontendForm extends Vps_Form
{
    protected function _init()
    {
        $this->setModel(new Vps_Model_FnF());
        $this->add(new Vps_Form_Field_TextField('query', ''));
        parent::_init();
    }

}
