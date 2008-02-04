<?php
class Vpc_Composite_Downloads_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        $data = new Vps_Auto_Data_Vpc_Table(
            'Vpc_Basic_Download_Model',
            'infotext',
            'Vpc_Basic_Download_Component'
        );

        $this->_columns->add(new Vps_Auto_Grid_Column($classes['child'], 'Filename', 200))
            ->setData($data);
        parent::_initColumns();
    }
}
