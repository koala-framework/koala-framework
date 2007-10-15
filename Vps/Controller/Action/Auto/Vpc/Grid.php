<?php
abstract class Vps_Controller_Action_Auto_Vpc_Grid extends Vps_Controller_Action_Auto_Grid
{
    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['page_id = ?'] = $this->component->getDbId();
        $where['component_key = ?'] = $this->component->getComponentKey();
        return $where;
    }

    protected function _beforeSave($row)
    {
        $row->page_id = $this->component->getDbId();
        $row->component_key = $this->component->getComponentKey();
    }

    public function indexAction()
    {
       $this->view->ext('Vps.Component.GridPanel');
    }
}