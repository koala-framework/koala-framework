<?php
// das ist das child model
class Kwc_Abstract_List_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_composite_list';
    protected $_rowClass = 'Kwc_Abstract_List_Row';

    protected $_referenceMap = array(
        'Component' => array(
            'refModelClass' => 'Kwc_Abstract_List_OwnModel',
            'column' => 'component_id'
        )
    );

    protected function _init()
    {
        $this->_siblingModels[] = new Kwf_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
