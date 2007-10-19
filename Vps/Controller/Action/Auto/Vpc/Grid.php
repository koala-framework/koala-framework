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
       $this->view->ext($this->component);
    }

    public function jsonInsertAction()
    {
        $row = $this->_table->createRow();
        $this->_beforeInsert($row);
        $this->_beforeSave($row);
        if ($this->_position) {
            $row->pos = 0;
        }
        $this->view->id = $row->save();

        if ($this->_position) {
            $row->numberize($this->_position, null, $this->_getWhere());
        }
    }

    protected function _beforeDelete($row)
    {
        $component = $this->component->images[$row->id];
        if ($component) {
            Vpc_Admin::getInstance($component)->delete($component);
        }
    }

}