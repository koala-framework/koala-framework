<?php
class Vpc_Composite_Images_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        if (Vpc_Abstract::getSetting($classes['child'], 'editComment')) {
            $data = new Vps_Auto_Data_Vpc_Table(
                'Vpc_Basic_Image_Model',
                'comment',
                'Vpc_Basic_Image_Component'
            );

            $this->_columns->add(new Vps_Auto_Grid_Column('comment', 'Comment', 200))
                ->setData($data);
        } else if (Vpc_Abstract::getSetting($classes['child'], 'editFilename')) {
            $data = new Vps_Auto_Data_Vpc_Table(
                'Vpc_Basic_Image_Model',
                'filename',
                'Vpc_Basic_Image_Component'
            );

            $this->_columns->add(new Vps_Auto_Grid_Column('filename', 'Filename', 200))
                ->setData($data);
        }

        $this->_columns->add(new Vps_Auto_Grid_Column($classes['child'], 'Image', 35))
            ->setData(new Vps_Auto_Data_Vpc_Image($classes['child'], $this->componentId));
        parent::_initColumns();
    }
}
