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
        if (!$this->is_home) $this->is_home = 0;
        if (!$this->visible) $this->visible = 0;
        if (!$this->pos) $this->pos = 1;
    }

    protected function _update()
    {
        parent::_update();
        if ($this->is_home && !$this->visible) {
            throw new Vps_ClientException(trlVps('Cannot set Home Page invisible'));
        }
    }

    protected function _delete()
    {
        parent::_delete();
        if (count($this->findDependentRowset('Vps_Dao_Pages'))) {
            throw new Vps_ClientException(trlVps("Can't delete page as there are child pages."));
        }

        // Dranhängende Komponente löschen
        $generators = Vps_Component_Data_Root::getInstance()->getPageGenerators();
        foreach ($generators as $generator) {
            $class = Vpc_Abstract::getChildComponentClass($generator->getClass(), null, $this->component);
            Vpc_Admin::getInstance($class)->delete($this->id);
        }
    }
}
