<?php
class Vps_Dao_Row_Page extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->name;
    }

    protected function _insert()
    {
        parent::_insert();
        if ($this->parent_id && !$this->type) {
            if (is_numeric($this->parent_id)) {
                $parentRow = $this->getTable()->find($this->parent_id)->current();
                $this->type = $parentRow->type;
            } else {
                $this->type = $this->parent_id;
                $this->parent_id = null;
            }
        }

        if (!$this->is_home) $this->is_home = 0;
        if (!$this->visible) $this->visible = 0;
        if (!$this->pos) $this->pos = 1;
    }

    protected function _update()
    {
        if ($this->is_home && !$this->visible) {
            throw new Vps_ClientException(trlVps('Cannot set Home Page invisible'));
        }
    }

    protected function _delete()
    {
        $data = $this->retrievePageData($id);

        if (count($this->findDependentRowset('Vps_Dao_Pages'))) {
            throw new Vps_ClientException(trlVps("Can't delete page as there are child pages."));
        }

        // Dranhängende Komponente löschen
        Vpc_Admin::getInstance($this->component_class)->delete($this->id);
    }
}
