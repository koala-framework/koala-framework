<?php
class Vpc_Directories_CategoryTree_Directory_Row extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->name;
    }

    protected function _delete()
    {
        if (count($this->findDependentRowset(get_class($this->getTable())))) {
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
        while (!is_null($upperRow = $upperRow->findParentRow(get_class($this->_getTable())))) {
            $parts[] = $upperRow;
        }
        return array_reverse($parts);
    }

    public function getRecursiveChildCategoryIds(array $where = array(), $parentId = null)
    {
        static $branchCache = array();
        if (!$branchCache) {
            $select = new Vps_Db_Table_Select($this->getTable());
            $select->from($this->getTable(), array('id', 'parent_id'));
            foreach ($where as $k => $v) {
                if (is_string($k)) {
                    $select->where($k, $v);
                } else {
                    $select->where($v);
                }
            }
            foreach ($select->query()->fetchAll() as $row) {
                $branchCache[$row['id']] = $row['parent_id'];
            }
        }

        if (is_null($parentId)) $parentId = $this->id;

        $ret = array($parentId);
        foreach (array_keys($branchCache, $parentId) as $v) {
            $ret[] = $v;
            $ret = array_merge($ret, $this->getRecursiveChildCategoryIds($where, $v));
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