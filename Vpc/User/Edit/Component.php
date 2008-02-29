<?php
class Vpc_User_Edit_Component extends Vpc_User_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => 'Account bearbeiten'
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    protected function _getEditRow()
    {
        $table = Zend_Registry::get('userModel');
        return $table->getAuthedUser();
    }
}
