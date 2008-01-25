<?php
class Vpc_Composite_Images_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $data = new Vps_Auto_Data_Vpc_Table(
            'Vpc_Basic_Image_Model',
            'filename',
            'Vpc_Basic_Image_Component'
        );

        $this->_columns->add(new Vps_Auto_Grid_Column('filename', 'Filename', 200))
            ->setData($data);

        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');
        $this->_columns->add(new Vps_Auto_Grid_Column($classes['child'], 'Image', 100))
            ->setData(new Vps_Auto_Data_Vpc_Image($classes['child'], $this->pageId, $this->componentKey));
        parent::_initColumns();
    }
}
