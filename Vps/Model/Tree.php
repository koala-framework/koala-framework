<?php
class Vps_Model_Tree extends Vps_Model_Db_Proxy
                        implements Vps_Model_Tree_Interface
{
    private $_parentIdsCache;
    private $_recursiveIdsCache;
    protected $_rowClass = 'Vps_Model_Tree_Row';
    protected $_useRecursiveIdsCache = false;

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['Parent'] = array(
            'column' => 'parent_id',
            'refModel' => $this
        );
        $this->_dependentModels['Childs'] = $this;
    }

    public function getRecursiveIdsCache()
    {
        if (!$this->useRecursiveIdsCache()) return false;

        if (!isset($this->_recursiveIdsCache)) {
            $this->_recursiveIdsCache = Vps_Cache::factory('Core', 'Apc', array(
                'automatic_serialization' => true
            ), array(
                'cache_id_prefix' => $this->getUniqueIdentifier().'_recid_',
            ));
        }
        return $this->_recursiveIdsCache;
    }

    public function useRecursiveIdsCache()
    {
        return $this->_useRecursiveIdsCache;
    }

    /**
     * @internal über row aufrufen!
     */
    public function getRecursiveIds($parentId)
    {
        $ret = false;
        if ($this->useRecursiveIdsCache()) {
            $ret = $this->getRecursiveIdsCache()->load((string)$parentId);
        }
        if ($ret === false) {
            if (!isset($this->_parentIdsCache)) {
                foreach ($this->export(Vps_Model_Interface::FORMAT_ARRAY, array()) as $row) {
                    $this->_parentIdsCache[$row[$this->getPrimaryKey()]]
                                = $row[$this->_referenceMap['Parent']['column']];
                }
            }
            $ret = array($parentId);
            foreach (array_keys($this->_parentIdsCache, $parentId) as $v) {
                $ret[] = $v;
                $ret = array_merge($ret, $this->getRecursiveIds($v));
            }

            $ret = array_values(array_unique($ret));
            if ($this->useRecursiveIdsCache()) {
                $this->getRecursiveIdsCache()->save($ret, (string)$parentId);
            }
        }
        return $ret;
    }

    public function getRootNodes($select = array())
    {
        $select = $this->select($select);
        return $this->getRows($select->whereNull('parent_id'));
    }

}
