<?php
class Vps_Crm_Customer_CommentController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vps_Crm_Customer_Model_Comments';

    protected function _initFields()
    {
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Comment')));

        $fs->add(new Vps_Form_Field_ShowField('insert_date', trlVps('Date')));
        $fs->add(new Vps_Form_Field_TextArea('value', trlVps('Text')))
            ->setWidth(270)
            ->setHeight(140);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->customer_id = $this->_getParam('customer_id');
    }
}