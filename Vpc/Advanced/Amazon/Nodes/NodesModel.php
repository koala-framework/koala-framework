<?php
class Vpc_Advanced_Amazon_Nodes_NodesModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_amazon_nodes';
    protected $_toStringField = 'name';

    protected $_referenceMap = array(
        'Field' => array(
            'column' => 'component_id',
            'refModelClass' => 'Vpc_Advanced_Amazon_Nodes_FieldModel'
        )
    );

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
