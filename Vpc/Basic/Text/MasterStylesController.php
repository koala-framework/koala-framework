<?php
class Vpc_Basic_Text_MasterStylesController extends Vpc_Basic_Text_InlineStylesController
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('tag', trlVps('Selector'), 80));
    }

    public function init()
    {
        if ($this->_getUserRole() != 'admin') {
            $this->_buttons = array();
        }
        parent::init();
    }

    protected function _getWhere()
    {
        $where = Vps_Controller_Action_Auto_Grid::_getWhere();
        $where[] = "master = 1";
        return $where;
    }
}
