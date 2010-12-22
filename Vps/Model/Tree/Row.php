<?php
class Vps_Model_Tree_Row extends Vps_Model_Proxy_Row
    implements IteratorAggregate, Vps_Model_Tree_Row_Interface
{
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
        return $this->getModel()->getRecursiveIds($this->id);
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
        return new Vps_Model_Tree_RecursiveIterator($this);
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        $this->getModel()->getRecursiveIdsCache()->clean();
    }

    protected function _afterDelete()
    {
        parent::_afterDelete();
        $this->getModel()->getRecursiveIdsCache()->clean();
    }
}
