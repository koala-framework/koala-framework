<?php
class Kwf_Util_Model_Amazon_Nodes extends Kwf_Model_Abstract
{
    protected $_dependentModels = array(
        'ProductsToNodes' => 'Kwf_Util_Model_Amazon_ProductsToNodes'
    );

    protected $_rowClass = 'Kwf_Util_Model_Amazon_Nodes_Row';
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
            $this->_amazon = new Kwf_Service_Amazon();
        }
        parent::_init();
    }
    public function getRow($select)
    {
        if (!$select) {
            throw new Kwf_Exception('getRow needs a parameter, null is not allowed.');
        }
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        if (count($select->getParts()) != 1) {
            throw new Kwf_Exception("only whereId in select allowed");
        }
        if ($select->getPart(Kwf_Model_Select::WHERE_ID)) {
            $BrowseNodeId = $select->getPart(Kwf_Model_Select::WHERE_ID);
        } else if ($w = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($w as $f=>$i) {
                if ($f != $this->getPrimaryKey()) {
                    throw new Kwf_Exception("only whereEquals with primaryKey in select allowed");
                }
                $BrowseNodeId = $i;
            }
        } else {
            throw new Kwf_Exception("only whereEquals or whereId in select allowed");
        }
        if (!isset($this->_rows[$BrowseNodeId])) {
            $options = array(
                'AssociateTag' => Kwf_Registry::get('config')->service->amazon->associateTag
            );
            $result = $this->_amazon->browseNodeLookup($BrowseNodeId, $options);
            $this->_rows[$BrowseNodeId] = new $this->_rowClass(array(
                'item' => $result,
                'model' => $this
            ));
        }
        return $this->_rows[$BrowseNodeId];
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function getPrimaryKey()
    {
        return 'browseNodeId';
    }

    protected function _getOwnColumns()
    {
        return array('browseNodeId', 'name');
    }

    public function getUniqueIdentifier()
    {
        throw new Kwf_Exception_NotYetImplemented();
    }
    public function transformColumnName($name)
    {
        $name = strtoupper(substr($name, 0, 1)).substr($name, 1);
        return $name;
    }
}
