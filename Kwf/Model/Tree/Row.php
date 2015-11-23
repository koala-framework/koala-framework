<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Tree_Row extends Kwf_Model_Proxy_Row
    implements IteratorAggregate, Kwf_Model_Tree_Row_Interface
{
    public function __get($name)
    {
        if ($name == 'tree_path') {
            return $this->getTreePath();
        }
        return parent::__get($name);
    }

    public function __isset($name)
    {
        if ($name == 'tree_path') return true;
        return parent::__isset($name);
    }

    public function getTreePath($separator = ' » ')
    {
        $ret = array();
        foreach ($this->getTreePathRows() as $row) {
            $ret[] = $row->__toString();
        }
        $ret = implode($separator, $ret);
        return $ret;
    }

    /**
     * Kann überschrieben werden um bestimmte Kategorien auszublenden, zB ProSalzburg die Root-Kategorie
     */
    public function getTreePathRows()
    {
        $parts = array($this);
        $upperRow = $this;
        while (!is_null($upperRow = $upperRow->getParentNode())) {
            $parts[] = $upperRow;
        }
        return array_reverse($parts);
    }

    public function getRecursiveIds()
    {
        $pk = $this->getModel()->getPrimaryKey();
        return $this->getModel()->getRecursiveIds($this->$pk);
    }

    public function getParentNode()
    {
        return $this->getParentRow('Parent');
    }

    public function getChildNodes($select = array())
    {
        return $this->getChildRows('Childs', $select);
    }

    public function getIterator()
    {
        return new Kwf_Model_Tree_RecursiveIterator($this);
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->getModel()->useRecursiveIdsCache()) {
            $this->getModel()->getRecursiveIdsCache()->clean();
        }
    }

    protected function _afterDelete()
    {
        parent::_afterDelete();
        if ($this->getModel()->useRecursiveIdsCache()) {
            $this->getModel()->getRecursiveIdsCache()->clean();
        }
    }
}
