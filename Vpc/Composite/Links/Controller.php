<?php
class Vpc_Composite_Links_Controller extends Vpc_Abstract_List_Controller
{
    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('text', 'Text', 200))
            ->setData(new Vps_Auto_Data_Vpc_Table(
                'Vpc_Basic_Link_Model',
                'text',
                'Vpc_Basic_Link_Component'
            ));

        parent::_initColumns();
    }
}
