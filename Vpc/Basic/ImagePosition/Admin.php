<?php
class Vpc_Basic_ImagePosition_Admin extends Vpc_Admin  
{
    protected function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row->getTable() instanceof Vps_Dao_Vpc) {
            Vps_Component_Cache::getInstance()->remove($this->_class, $row->component_id);
        }
    }
}
