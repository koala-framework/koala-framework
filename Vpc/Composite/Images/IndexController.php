<?php
class Vpc_Composite_Images_IndexController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_tableName = 'Vpc_Composite_Images_IndexModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('filename', 'Filename', 200))
            ->setData(new Vps_Auto_Data_Vpc_Table('Vpc_Basic_Image_IndexModel', 'filename'));
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', 'Visible', 100))
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'));
        $this->_columns->add(new Vps_Auto_Grid_Column('image', 'Image', 100))
            ->setData(new Vps_Auto_Data_Vpc_Image($this->component));
    }

    protected function _beforeInsert($row)
    {
        $row->visible = 0;
    }
}