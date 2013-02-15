<?php
class Kwc_Tags_Suggestions_GridController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Tags_Suggestions_Model';
    protected $_buttons = array('save');
    protected $_paging = 25;

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('tag_name', trl('Tag'), 200));
        $this->_columns->add(new Kwf_Grid_Column('user_email', trl('Benutzer'), 200));
        $this->_columns->add(new Kwf_Grid_Column('tag_count_used', trl('Verwendet'), 50));

        $this->_columns->add(new Kwf_Grid_Column_Checkbox('deny', trl('IGNOR'), 50))
            ->setEditor(new Kwf_Form_Field_Checkbox())
            ->setData(new Kwc_Tags_Suggestions_DenyData());
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('accept', trl('OK'), 50))
            ->setData(new Kwc_Tags_Suggestions_AcceptData())
            ->setEditor(new Kwf_Form_Field_Checkbox());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('status', 'new');
        return $ret;
    }
}
