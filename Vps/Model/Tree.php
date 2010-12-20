<?php
class Vps_Model_Tree extends Vps_Model_Db_Proxy
                        implements Vps_Model_Tree_Interface
{
    private $_parentIdsCache;
    protected $_rowClass = 'Vps_Model_Tree_Row';

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['Parent'] = array(
            'column' => 'parent_id',
            'refModel' => $this
        );
        $this->_dependentModels['Childs'] = $this;
    }

    /**
     * @internal über row aufrufen!
     */
    public function getRecursiveIds($parentId)
    {
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

        return array_values(array_unique($ret));
    }

    public function getRootNodes($select = array())
    {
        $select = $this->select($select);
        return $this->getRows($select->whereNull('parent_id'));
    }

}
