<?php
class Vps_Model_Tree extends Vps_Model_Db_Proxy
{
    private $_parentIdsCache;
    protected $_rowClass = 'Vps_Model_Tree_Row';

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['Parent'] = array(
            'column' => 'parent_id',
            'refModel' => $this,
            'refColumn' => 'id'
        );
        $this->_dependentModels['Childs'] = $this;
    }

    /**
     * besser über row aufrufen!
     */
    public function getRecursiveChildIds($parentId)
    {
        if (!isset($this->_parentIdsCache)) {
            foreach ($this->export(self::FORMAT_ARRAY) as $row) {
                $this->_parentIdsCache[$row['id']] = $row['parent_id'];
            }
        }
        $ret = array();
        foreach (array_keys($this->_parentIdsCache, $parentId) as $v) {
            $ret[] = $v;
            $ret = array_merge($ret, $this->getRecursiveChildIds($v));
        }

        return array_values(array_unique($ret));
    }

}
