<?php
class Kwc_Articles_Directory_AuthorsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Articles_Directory_AuthorsModel';
    protected $_buttons = array('save', 'add');

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('token', trlKwf('Contraction'), 100))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('First name'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Last name'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('feedback_email', trlKwf('Feedback E-Mail'), 300))
            ->setEditor(new Kwf_Form_Field_EMailField());
    }
}
