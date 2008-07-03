<?php
class Vpc_News_Directory_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        $class = $this->getTable()->getComponentClass();
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->component_id . '-' . $this->id);
        }
        Vps_Component_Cache::getInstance()->cleanByTag('news');
    }

    public function __toString()
    {
        return $this->title;
    }

    protected function _update()
    {
        Vps_Component_Cache::getInstance()->cleanByTag('news');
    }
    protected function _insert()
    {
        Vps_Component_Cache::getInstance()->cleanByTag('news');
    }
}
