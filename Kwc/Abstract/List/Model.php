<?php
// das ist das child model
class Vpc_Abstract_List_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_composite_list';
    protected $_rowClass = 'Vpc_Abstract_List_Row';

    protected $_referenceMap = array(
        'Component' => array(
            'refModelClass' => 'Vpc_Abstract_List_OwnModel',
            'column' => 'component_id'
        )
    );

    protected $_default = array(
        'visible' => 1
    );

    protected function _init()
    {
        $this->_siblingModels[] = new Vps_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
