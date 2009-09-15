<?php
class Vpc_Guestbook_Controller extends Vpc_Directories_Item_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_filters = array('text' => true);

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column_Checkbox('visible', trlVps('Visible'), 40))
            ->setEditor(new Vps_Form_Field_Checkbox());
        $this->_columns->add(new Vps_Grid_Column_Date('create_time', trlVps('Create Date')));
        $this->_columns->add(new Vps_Grid_Column('content', trlVps('Content'), 350));
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Name'), 130));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('E-Mail'), 150));
        $this->_columns->add(new Vps_Grid_Column_Button('editdialog'));
    }
}
