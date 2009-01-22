<?php
class Vps_Util_Model_Amazon_Nodes extends Vps_Model_Abstract
{
    protected $_dependentModels = array(
        'ProductsToNodes' => 'Vps_Util_Model_Amazon_ProductsToNodes'
    );

    protected $_rowClass = 'Vps_Util_Model_Amazon_Products_Row';
    protected $_toStringField = 'name';

    protected $_responseGroup = 'Small,BrowseNodes';

    protected $_amazon;
    protected $_items;

    public function __construct(array $config = array())
    {
        if (isset($config['amazon'])) $this->_amazon = (array)$config['amazon'];
        parent::__construct($config);
    }

    protected function _init()
    {
        if (!$this->_amazon) {
            $this->_amazon = new Vps_Service_Amazon();
        }
        parent::_init();
    }
    public function getRow($select)
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        if (count($select->getParts()) != 1) {
            throw new Vps_Exception("only whereId in select allowed");
        }
        if ($select->getPart(Vps_Model_Select::WHERE_ID)) {
            $BrowseNodeId = $select->getPart(Vps_Model_Select::WHERE_ID);
        } else if ($w = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($w as $f=>$i) {
                if ($f != $this->getPrimaryKey()) {
                    throw new Vps_Exception("only whereEquals with primaryKey in select allowed");
                }
                $BrowseNodeId = $i;
            }
        } else {
            throw new Vps_Exception("only whereEquals or whereId in select allowed");
        }
        if (!isset($this->_rows[$BrowseNodeId])) {
            $result = $this->_amazon->browseNodeLookup($BrowseNodeId);
            $this->_rows[$BrowseNodeId] = new $this->_rowClass(array(
                'item' => $result,
                'model' => $this
            ));
        }
        return $this->_rows[$BrowseNodeId];
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    public function getPrimaryKey()
    {
        return 'browseNodeId';
    }

    public function getColumns()
    {
        return array('browseNodeId', 'name');
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
