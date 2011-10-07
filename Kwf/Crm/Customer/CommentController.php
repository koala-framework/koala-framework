<?php
class Kwf_Crm_Customer_CommentController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Kwf_Crm_Customer_Model_Comments';

    protected function _initFields()
    {
        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Comment')));

        $fs->add(new Kwf_Form_Field_ShowField('insert_uid', trlKwf('User')))
            ->setData(new Kwf_Data_Table_Parent('InsertUser'));
        $fs->add(new Kwf_Form_Field_ShowField('insert_date', trlKwf('Date')));
        $fs->add(new Kwf_Form_Field_TextArea('value', trlKwf('Text')))
            ->setWidth(270)
            ->setHeight(140);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->customer_id = $this->_getParam('customer_id');
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($authedUser) $row->insert_uid = $authedUser->id;
    }
}
