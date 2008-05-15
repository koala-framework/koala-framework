<?php
class Vpc_Basic_Text_MasterStylesController extends Vpc_Basic_Text_InlineStylesController
{
    public function init()
    {
        if ($this->_getUserRole() != 'admin') {
            $this->_buttons = array();
        }
    }

    protected function _getWhere()
    {
        $where = Vps_Controller_Action_Auto_Grid::_getWhere();
        $where[] = "master = 1";
        return $where;
    }
}
