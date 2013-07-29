<?php
class Kwc_FulltextSearch_Search_Directory_Model extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_Row_Data_Abstract';
    protected $_rowsetClass = 'Kwf_Model_Rowset_Abstract';

    public function getPrimaryKey()
    {
        return 'id';
    }

    protected function _getOwnColumns()
    {
        return array(
            'id',
            'data',
            'content',
            'title'
        );
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $this->_data[$key],
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    private function _query($select)
    {
        $limitCount = $select->getPart(Kwf_Model_Select::LIMIT_COUNT);
        $limitOffset = $select->getPart(Kwf_Model_Select::LIMIT_OFFSET);
        if ($limitOffset === null) $limitOffset = 0;

        $queryString = '';
        $params = array();
        if ($select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($select->getPart(Kwf_Model_Select::WHERE_EXPRESSION) as $exp) {
                if ($exp instanceof Kwf_Model_Select_Expr_SearchLike) {
                    foreach ($exp->getSearchValues() as $field=>$value) {
                        if ($field == 'query') {
                            $queryString = $value;
                        }
                    }
                }
            }
        }
        if ($select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS) as $field=>$value) {
                if (isset($params['fq'])) {
                    //if more than one fields should be queried they are apended with +
                    $params['fq'] .= '+'.'-'.$field.':'.$value;
                } else {
                    //format is: -field:value for negation
                    $params['fq'] = '-'.$field.':'.$value;
                }
            }
        }

        if ($id = $select->getPart(Kwf_Model_Select::WHERE_ID)) {
            throw new Kwf_Exception_NotYetImplemented();
        }

        $subroot = Kwf_Component_Data_Root::getInstance();
        if ($select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($select->getPart(Kwf_Model_Select::WHERE_EQUALS) as $field=>$value) {
                if ($field == 'subroot') {
                    $subroot = $value;
                }
            }
        }

        $res = Kwf_Util_Fulltext_Backend_Abstract::getInstance()
            ->userSearch($subroot, $queryString, $limitOffset, $limitCount, $params);

        if ($res['error']) {
            throw new Kwf_Exception("Fulltext error: ".$res['error']);
        }
        return $res;
    }

    public function getRows($where = null, $order = null, $limit = null, $start = null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }

        $whereId = $select->getPart(Kwf_Model_Select::WHERE_ID);
        if ($eq = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($eq as $field=>$value) {
                if ($field == 'id') {
                    $whereId = $value;
                } else if ($field != 'subroot') {
                    throw new Kwf_Exception_NotYetImplemented();
                }
            }
        }

        if ($whereId) {
            if (isset($this->_rows[$whereId])) {
                $dataKeys = array($whereId);
                return new $this->_rowsetClass(array(
                    'model' => $this,
                    'dataKeys' => $dataKeys
                ));
            }
        }

        $res = $this->_query($select);

        $dataKeys = array();
        foreach ($res['hits'] as $h) {
            $id = md5($h['data']->componentId);
            $this->_data[$id] = $h;
            $this->_data[$id]['id'] = $id;
            $dataKeys[] = $id;
        }

        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => $dataKeys
        ));
    }

    public function countRows($select = array())
    {
        if (!is_object($select)) $select = $this->select($select);
        $res = $this->_query($select);
        return $res['numHits'];
    }
}
