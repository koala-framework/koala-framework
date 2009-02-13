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
        if ($c = $select->getPart(Vps_Model_Select::LIMIT_COUNT)) {
            if ($c > 10) {
                throw new Vps_Exception('limitCount can\'t be higher than 10');
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
        if ($offs = $select->getPart(Vps_Model_Select::LIMIT_OFFSET)) {
            $options['ItemPage'] = floor($offs/10) + 1;
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
            $limitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
            $limitOffset = $select->getPart(Vps_Model_Select::LIMIT_OFFSET);
            $limitOffset = $limitOffset - (floor($limitOffset/10)*10);
            $i = 0;
            $dataKeys = array();
            foreach ($results as $result) {
                $i++;
                if ($limitOffset >= $i) continue;
                $dataKeys[] = $result->ASIN;
                $this->_items[$result->ASIN] = $result;
                if ($limitCount && count($dataKeys) >= $limitCount) break;
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
        if (!isset($options['ItemPage'])) {
            $options['ItemPage'] = 1;
        }
        $page = $options['ItemPage'];
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

    public function getOwnColumns()
    {
        return array('asin', 'title', 'detailPageURL', 'currencyCode', 'amount', 'formattedPrice',
                     'salesRank', 'averageRating', 'author');
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
