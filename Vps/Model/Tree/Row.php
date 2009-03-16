<?php
class Vps_Model_Tree_Row extends Vps_Model_Proxy_Row implements IteratorAggregate
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
        while (!is_null($upperRow = $upperRow->getParentRow('Parent'))) {
            $parts[] = $upperRow;
        }
        return array_reverse($parts);
    }

    public function getRecursiveIds()
    {
        return $this->getModel()->getRecursiveIds($this->id);
    }

    public function getIterator()
    {
        return new Vps_Model_Tree_RecursiveIterator($this);
    }
}
