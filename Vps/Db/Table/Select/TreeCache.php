<?php
class Vps_Db_Table_Select_TreeCache extends Vps_Db_Table_Select
{
    private $_treeCacheClass = null;
    public function setTreeCacheClass($v)
    {
        $this->_treeCacheClass = $v;
        return $this;
    }
    public function getTreeCacheClass()
    {
        return $this->_treeCacheClass;
    }
}
