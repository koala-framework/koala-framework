<?php
class Vpc_Directories_Item_Directory_Trl_AdminModel extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vpc_Directories_Item_Directory_Trl_AdminModelRow';
    protected $_rowsetClass = 'Vpc_Directories_Item_Directory_Trl_AdminModelRowset';
    protected $_trlModel;

    public function __construct($config)
    {
        if (isset($config['proxyModel'])) $config['proxyModel'] = Vps_Model_Abstract::getInstance($config['proxyModel']);
        if (isset($config['trlModel'])) $this->_trlModel = Vps_Model_Abstract::getInstance($config['trlModel']);
        parent::__construct($config);
    }

    public function createRow(array $data = array())
    {
        throw new Vps_Exception("Not possible");
    }

    protected function _getComponentId($select)
    {
        $componentId = null;
        foreach ($select->getPart(Vps_Model_Select::WHERE_EQUALS) as $k=>$i) {
            if ($k == 'component_id') $componentId = $i;
        }
        if (!$componentId) throw new Vps_Exception_NotYetImplemented();
        return $componentId;
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $select = $this->select($where, $order, $limit, $start);
        $componentId = $this->_getComponentId($select);

        if ($componentId) {
            $c = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
            $select->whereEquals('component_id', $c->chained->dbId);
        }

        $proxyRowset = $this->_proxyModel->getRows($select);
        return new $this->_rowsetClass(array(
            'rowset' => $proxyRowset,
            'rowClass' => $this->_rowClass,
            'model' => $this,
            'componentId' => $componentId
        ));
    }

    public function countRows($where = array())
    {
        $select = $this->select($where);
        $componentId = $this->_getComponentId($select);

        if ($componentId) {
            $c = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
            $select->whereEquals('component_id', $c->chained->dbId);
        }

        return $this->_proxyModel->countRows($select);
    }

    public function getRowByProxiedRow($proxiedRow, $componentId)
    {
        $id = $proxiedRow->getInternalId().$componentId;
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

    protected function _getTrlRow($proxiedRow, $componentId)
    {
        $proxyId = $componentId . '_' . $proxiedRow->id;
        $trlRow = $this->_trlModel->getRow($proxyId);
        if (!$trlRow) {
            $trlRow = $this->_trlModel->createRow();
            $trlRow->component_id = $proxyId;
        }
        return $trlRow;
    }
}
