<?php
class Vpc_Posts_Latest_Admin extends Vpc_Admin
{
    public function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Vpc_Forum_Directory_Row) {
            Vps_Component_Cache::getInstance()->remove($this->_class);
        }
    }
}
