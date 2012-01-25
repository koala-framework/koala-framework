<?php
/**
 * @package Model
 */
class Kwf_Model_Tree extends Kwf_Model_Db_Proxy
                        implements Kwf_Model_Tree_Interface
{
    private $_parentIdsCache;
    private $_recursiveIdsCache;
    protected $_rowClass = 'Kwf_Model_Tree_Row';
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
            $cacheId = 'recid-'.$this->getUniqueIdentifier().'-'.(string)$parentId;
            $ret = Kwf_Cache_Simple::fetch($cacheId);
        }
        if ($ret === false) {
            if (!isset($this->_parentIdsCache)) {
                foreach ($this->export(Kwf_Model_Interface::FORMAT_ARRAY, array()) as $row) {
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
                Kwf_Cache_Simple::add($cacheId, $ret);
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
