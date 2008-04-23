<?php
class Vps_Dao_Row_Page extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->name;
    }

    public function _insert()
    {
        parent::_insert();
        if ($this->parent_id && !$this->type) {
            $parentRow = $this->getTable()->find($this->parent_id)->current();
            $this->type = $parentRow->type;
        }
        if (!$this->is_home) $this->is_home = 0;
        if (!$this->visible) $this->visible = 0;
        if (!$this->pos) $this->pos = 1;
    }
}
