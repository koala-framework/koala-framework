<?php
class Kwc_Basic_Table_Trl_AdminModel extends Kwf_Model_Proxy
{
    protected $_rowClass = 'Kwc_Basic_Table_Trl_AdminRow';
    protected $_rowsetClass = 'Kwc_Basic_Table_Trl_AdminModelRowset';
    protected $_trlModel;

    public function __construct($config)
    {
        if (isset($config['proxyModel'])) $config['proxyModel'] = Kwf_Model_Abstract::getInstance($config['proxyModel']);
        if (isset($config['trlModel'])) $this->_trlModel = Kwf_Model_Abstract::getInstance($config['trlModel']);
        parent::__construct($config);
    }

    /**
     * Extracts the component id from select
     */
    protected function _getComponentId($select)
    {
        $componentId = null;
        if ($select) {
            if ($select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
                foreach ($select->getPart(Kwf_Model_Select::WHERE_EQUALS) as $k=>$i) {
                    if ($k == 'component_id') $componentId = $i;
                }
            }
        }
        return $componentId;
    }

    /**
     * returns the related trl-row
     */
    protected function _getTrlRow($proxiedRow, $componentId)
    {
        $row = $proxiedRow;
        $proxyId = $proxiedRow->id;
        $select = $this->_trlModel->select()
            ->whereEquals('component_id', $componentId)
            ->whereEquals('master_id', $proxyId);
        $trlRow = $this->_trlModel->getRows($select)->current();
        if ($trlRow) {
            $row = $trlRow;
        }
        return $row;
    }

    /**
     * Should return the specified row, componentId and id have to be defined
     */
    public function getRow($select)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    /**
     * Returns rows, including trl
     */
    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $select = $this->select($where, $order, $limit, $start);
        $componentId = $this->_getComponentId($select);

        if ($componentId) { // get master-component-id
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($componentId, array('ignoreVisible'=>true));
            $select->whereEquals('component_id', $c->chained->componentId);
        }
        $proxyRowset = $this->_proxyModel->getRows($select);
        return new $this->_rowsetClass(array(
            'rowset' => $proxyRowset,
            'rowClass' => $this->_rowClass,
            'model' => $this,
            'componentId' => $componentId
        ));
    }

    /**
     * Returns count of master-rows
     */
    public function countRows($where = array())
    {
        $select = $this->select($where);
        $componentId = $this->_getComponentId($select);

        if ($componentId) {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($componentId, array('ignoreVisible'=>true));
            $select->whereEquals('component_id', $c->chained->componentId);
        }

        return $this->_proxyModel->countRows($select);
    }

    /**
     * gets the trl-row and adds it to the row
     */
    public function getRowByProxiedRow($proxiedRow, $componentId)
    {
        $id = $proxiedRow->getInternalId();
        if (!isset($this->_rows[$id])) {
            $trlRow = $this->_getTrlRow($proxiedRow, $componentId);
            $this->_rows[$id] = new $this->_rowClass(array(
                'row' => $proxiedRow,
                'model' => $this,
                'exprValues' => $this->_getExprValues($proxiedRow),
                'trlRow' => $trlRow
            ));
        }
        return $this->_rows[$id];
    }

    public function createRow(array $data = array())
    {
        throw new Kwf_Exception("Not possible");
    }
}
