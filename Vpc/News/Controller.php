<?php
class Vpc_News_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'reload' => true,
        'add'   => true
    );
    protected $_defaultOrder = array('field' => 'publish_date', 'direction' => 'DESC');
    protected $_editDialog = array('controllerUrl'=>'edit/Vpc_News_Form',
                                   'width'=>500,
                                   'height'=>400);
    
    public function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('title', 'Title', 300));
        $this->_columns->add(new Vps_Auto_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/table_edit.png')
            ->setToolTip('Edit News');
        $this->_columns->add(new Vps_Auto_Grid_Column('publish_date', 'Publish Date', 50))
            ->setEditor(new Vps_Auto_Field_DateField('publish_date', 'Publish Date'));
        $this->_columns->add(new Vps_Auto_Grid_Column('expiry_date', 'Expiry Date', 50))
            ->setEditor(new Vps_Auto_Field_DateField('expiry_date', 'Expiry Date'));
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', 'Visible', 20))
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'));
    }
}
