<?php
class Kwc_Articles_Detail_TagsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Articles_Directory_TagsModel';
    protected $_buttons = array();
    protected $_defaultOrder = array('field'=>'count_used', 'direction'=>'DESC');

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('name'));
        $this->_columns->add(new Kwf_Grid_Column('count_used'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('valuesqry') == 'true') {
            $ret = new Kwf_Model_Select();
            $ret->whereEquals('id', explode('|', $this->_getParam('query')));
        }
        $ret->whereEquals('type', 'tag');
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }

    public function jsonAddItemAction()
    {
        if (!$this->_getParam('val')) throw new Kwf_Exception("val required");
        $s = $this->_getModel()->select();
        $s->whereEquals('name', $this->_getParam('val'));
        $s->whereEquals('type', 'tag');
        if ($row = $this->_getModel()->getRow($s)) {
            $this->view->id = $row->id;
            $this->view->name = $row->name;
        } else {
            $row = $this->_getModel()->createRow();
            $row->name = $this->_getParam('val');
            $row->type = 'tag';
            $row->save();
        }
        $this->view->id = $row->id;
        $this->view->name = $row->name;
    }
}
