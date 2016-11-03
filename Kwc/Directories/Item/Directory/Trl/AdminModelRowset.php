<?php
class Kwc_Directories_Item_Directory_Trl_AdminModelRowset extends Kwf_Model_Proxy_Rowset
{
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_componentId = $config['componentId'];
    }

    public function current()
    {
        $row = $this->_rowset->current();
        if (is_null($row)) return null;
        return $this->_model->getRowByProxiedRowAndComponentId($row, $this->_componentId);
    }
}
