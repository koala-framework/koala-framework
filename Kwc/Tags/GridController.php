<?php
class Kwc_Tags_GridController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Tags_Model';
    protected $_buttons = array('save', 'add', 'delete');
    protected $_paging = 25;
    protected $_filters = array('text'=>true);
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $editor = new Kwf_Form_Field_TextField();
        $editor->addValidator(new Kwf_Validate_Row_Unique());
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 200))
            ->setEditor($editor);
    }
}
