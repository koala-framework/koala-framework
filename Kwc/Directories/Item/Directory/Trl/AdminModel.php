<?php
/**
 * This model is a proxy model that proxies the master and adds additional columns as from trl (implemented in row)
 */
class Kwc_Directories_Item_Directory_Trl_AdminModel extends Kwf_Model_Proxy
{
    protected $_rowClass = 'Kwc_Directories_Item_Directory_Trl_AdminModelRow';
    protected $_rowsetClass = 'Kwc_Directories_Item_Directory_Trl_AdminModelRowset';
    protected $_trlModel;

    public function __construct($config)
    {
        if (isset($config['proxyModel'])) $config['proxyModel'] = Kwf_Model_Abstract::getInstance($config['proxyModel']);
        if (isset($config['trlModel'])) $this->_trlModel = Kwf_Model_Abstract::getInstance($config['trlModel']);
        if (!$this->_trlModel) {
            throw new Kwf_Exception('Kwc_Directories_Item_Directory_Trl needs to be set a child model.');
        }
        parent::__construct($config);
    }

    public function createRow(array $data = array())
    {
        throw new Kwf_Exception("Not possible");
    }

    protected function _getComponentId($select)
    {
        $componentId = null;
        foreach ($select->getPart(Kwf_Model_Select::WHERE_EQUALS) as $k=>$i) {
            if ($k == 'component_id') $componentId = $i;
        }
        return $componentId;
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $select = $this->select($where, $order, $limit, $start);
        $componentId = $this->_getComponentId($select);

        if ($componentId) {
            $c = Kwf_Component_Data_Root::getInstance()
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

    public function getRow($select)
    {
        if (!is_object($select)) $select = new Kwf_Model_Select($select);

        $id = null;
        if ($select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($select->getPart(Kwf_Model_Select::WHERE_EQUALS) as $k=>$i) {
                if ($k == 'id') $id = $i;
            }
        }
        $componentId = $this->_getComponentId($select);
        if ($id && !$componentId) {
            //only id passed, in detail form controller
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($id, array('ignoreVisible'=>true));
            $select->whereEquals('component_id', $c->parent->chained->dbId);
            $select->whereEquals('id', $c->id);
            $componentId = $c->parent->dbId;
        } else if ($componentId && $id) {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
            $select->whereEquals('component_id', $c->chained->dbId);
            $select->whereEquals('id', $id);
        } else {
            throw new Kwf_Exception("invalid select");
        }
        $proxyRow = $this->_proxyModel->getRow($select);
        return $this->getRowByProxiedRow($proxyRow, $componentId);
    }

    public function countRows($where = array())
    {
        $select = $this->select($where);
        $componentId = $this->_getComponentId($select);

        if ($componentId) {
            $c = Kwf_Component_Data_Root::getInstance()
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
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        $sep = $c->getGenerator('detail')->getIdSeparator();
        $proxyId = $componentId . $sep . $proxiedRow->id;
        $trlRow = $this->_trlModel->getRow($proxyId);
        if (!$trlRow) {
            $trlRow = $this->_trlModel->createRow();
            $trlRow->component_id = $proxyId;
        }
        return $trlRow;
    }
}
