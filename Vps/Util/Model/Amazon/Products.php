<?php
class Vps_Util_Model_Amazon_Products extends Vps_Model_Abstract
{
    protected $_dependentModels = array(
        'ProductsToNodes' => 'Vps_Util_Model_Amazon_ProductsToNodes'
    );

    protected $_rowClass = 'Vps_Util_Model_Amazon_Products_Row';
    protected $_toStringField = 'title';

    protected $_responseGroup = 'Small,BrowseNodes,Similarities,ItemAttributes,Reviews,EditorialReview,Images';

    private $_amazon;
    private $_items;
    private $_resultsCache;

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

    public function countRows($select = array())
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $options = $this->_getOptions($select);
        $cacheId = $this->_itemSearchCacheId($options);
        if (isset($this->_resultsCache[$cacheId])) {
            reset($this->_resultsCache[$cacheId]);
            $results = current($this->_resultsCache[$cacheId]);
        } else {
            $results = $this->_itemSearch($options);
        }
        return $results->totalResults();
    }
    private function _getOptions($select)
    {
        $options = array();
        if ($select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($select->getPart(Vps_Model_Select::WHERE_EQUALS) as $f=>$v) {
                $options[$f] = $v;
            }
        }
        if ($select->getPart(Vps_Model_Select::WHERE_ID)) {
            $options['asin'] = $select->getPart(Vps_Model_Select::WHERE_ID);
        }
        if (isset($options['asin'])) {
            //wenn nach asin gesucht wird alles andere ignorieren
            $o = $options;
            $options = array('asin'=>$o['asin']);
            if (isset($o['AssociateTag'])) {
                $options['AssociateTag'] = $o['AssociateTag'];
            }
        }
        if ($select->getPart(Vps_Model_Select::LIMIT_COUNT) && $select->getPart(Vps_Model_Select::LIMIT_COUNT) != 10) {
            if (!isset($options['asin'])) {
                throw new Vps_Exception('limitCount must be 10');
            }
        }
        if ($offs = $select->getPart(Vps_Model_Select::LIMIT_OFFSET)) {
            if ($offs % 10 != 0) {
                throw new Vps_Exception('limitOffset must be in 10er steps');
            }
            $options['ItemPage'] = $offs/10 + 1;
        }
        if ($ord = $select->getPart(Vps_Model_Select::ORDER)) {
            $ord = array_values($ord);
            if (sizeof($ord) > 1) {
                throw new Vps_Exception('There can only be one order');
            }
            $ord = $ord[0];
            if ($ord['direction'] != 'ASC') {
                throw new Vps_Exception('you can only sort by ASC');
            }
            $options['Sort'] = $ord['field'];
        }
        $options['ResponseGroup'] = $this->_responseGroup;
        return $options;
    }
    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $options = $this->_getOptions($select);
        if (isset($options['asin'])) {
            $asin = $options['asin'];
            unset($options['asin']);
            if (!isset($this->_items[$asin])) {
                $result = $this->_amazon->itemLookup($asin, $options);
                $this->_items[$asin] = $result;
            }
            $dataKeys = array($asin);
        } else {
            $results = $this->_itemSearch($options);
            foreach ($results as $result) {
                $dataKeys[] = $result->ASIN;
                $this->_items[$result->ASIN] = $result;
            }
        }
        return new $this->_rowsetClass(array(
            'dataKeys' => $dataKeys,
            'model' => $this
        ));
    }

    private function _itemSearchCacheId($options)
    {
        if (isset($options['ItemPage'])) {
            unset($options['ItemPage']);
        }
        return serialize($options);
    }

    private function _itemSearch($options)
    {
        $cacheId = $this->_itemSearchCacheId($options);
        if (isset($options['ItemPage'])) {
            $page = $options['ItemPage'];
        } else {
            $page = 0;
        }
        if (!isset($this->_resultsCache[$cacheId][$page])) {
            $this->_resultsCache[$cacheId][$page] = $this->_amazon->itemSearch($options);
        }
        return $this->_resultsCache[$cacheId][$page];
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'item' => $this->_items[$key],
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    public function getPrimaryKey()
    {
        return 'asin';
    }

    public function getColumns()
    {
        return array('asin', 'title', 'detailPageURL', 'currencyCode', 'amount', 'formattedPrice',
                     'salesRank', 'averageRating', 'author');
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
