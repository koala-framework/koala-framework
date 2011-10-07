<?php
class Kwc_Advanced_Amazon_Nodes_NodesModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_amazon_nodes';
    protected $_toStringField = 'name';

    protected $_referenceMap = array(
        'Field' => array(
            'column' => 'component_id',
            'refModelClass' => 'Kwc_Advanced_Amazon_Nodes_FieldModel'
        )
    );

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
