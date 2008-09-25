<?php
class Vpc_Forum_LatestThreads_Admin extends Vpc_Admin
{
    public function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Vpc_Forum_Directory_Row ||
            $row instanceof Vpc_Forum_Group_Row ||
            $row instanceof Vpc_Posts_Directory_Row)
        {
            Vps_Component_Cache::getInstance()->cleanComponentClass($this->_class);
        }
    }
}