<?php
class Vpc_User_Directory_CommunityView_SearchForm_FrontendForm extends Vps_Form
{
    protected function _init()
    {
        $this->setModel(new Vps_Model_FnF());
        $this->add(new Vps_Form_Field_TextField('query', trlVps('Name')));
        parent::_init();
    }
}
