<?php
class Vpc_Composite_Downloads_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');

        $data = new Vps_Data_Vpc_Table(
            'Vpc_Basic_Download_Model',
            'infotext',
            'Vpc_Basic_Download_Component'
        );

        $this->_columns->add(new Vps_Grid_Column($class, trlVps('Descriptiontext'), 200))
            ->setData($data);
        parent::_initColumns();
    }
}
