<?php
class Kwc_Posts_Latest_Admin extends Kwc_Admin
{
    public function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Kwc_Forum_Directory_Row) {
            Kwf_Component_Cache::getInstance()->cleanComponentClass($this->_class);
        }
    }
}
