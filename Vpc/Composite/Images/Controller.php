<?php
class Vpc_Composite_Images_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('filename', 'Filename', 200))
            ->setData(new Vps_Auto_Data_Vpc_Table('Vpc_Basic_Image_Model', 'filename', $this->class));
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', 'Visible', 100))
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'));
            
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');
        $this->_columns->add(new Vps_Auto_Grid_Column($classes['image'], 'Image', 100))
            ->setData(new Vps_Auto_Data_Vpc_Image($classes['image'], $this->pageId, $this->componentKey));
    }

    protected function _beforeInsert($row)
    {
        $row->visible = 0;
    }
}
