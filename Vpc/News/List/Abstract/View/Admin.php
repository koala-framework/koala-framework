<?php
class Vpc_News_List_Abstract_View_Admin extends Vpc_Admin
{
    public function onRowUpdate($row)
    {
        if ($row instanceof Vpc_News_Directory_Row) {
            Vps_Component_Cache::getInstance()->remove($this->_class);
        }
    }
}
