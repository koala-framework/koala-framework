<?php
class Vpc_Directories_CategoryTree_Directory_Row extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->name;
    }

    protected function _delete()
    {
        if (count($this->getChildRows('Child'))) {
            throw new Vps_ClientException(
                trlVps("This category can't be deleted, because there exist sub-categories in it.")
            );
        }
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
        while (!is_null($upperRow = $upperRow->getParentRow('Parent'))) {
            $parts[] = $upperRow;
        }
        return array_reverse($parts);
    }

    public function getRecursiveChildCategoryIds($select = null, $parentId = null)
    {
        static $branchCache = array();
        if (!$branchCache) {
            if (is_null($select)) $select = new Vps_Model_Select();
            if (!$select instanceof Vps_Model_Select) {
                throw new Vps_Exception("First argument must be an object that instantiates 'Vps_Model_Select'");
            }

            $rows = $this->getModel()->getRows($select);
            foreach ($rows as $row) {
                $branchCache[$row->id] = $row->parent_id;
            }
        }

        if (is_null($parentId)) $parentId = $this->id;

        $ret = array($parentId);
        foreach (array_keys($branchCache, $parentId) as $v) {
            $ret[] = $v;
            $ret = array_merge($ret, $this->getRecursiveChildCategoryIds($select, $v));
        }

        return array_values(array_unique($ret));
    }

    public function __isset($name)
    {
        if ($name == 'name_path') {
            return true;
        } else {
            return parent::__isset($name);
        }
    }

    public function __get($name)
    {
        if ($name == 'name_path') {
            return $this->getTreePath();
        } else {
            return parent::__get($name);
        }
    }
}