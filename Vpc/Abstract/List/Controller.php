<?php
class Vpc_Abstract_List_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');

    protected function _initColumns()
    {
        if (Vpc_Abstract::getSetting($this->_getParam('class'), 'showPosition')) {
            $this->_position = 'pos';
        } else {
            $this->_position = false;
        }
        parent::_initColumns();
        
        
        $c = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
        foreach (Vpc_Admin::getInstance($c)->gridColumns() as $i) {
            $this->_columns->add($i);
        }
        
        if (Vpc_Abstract::getSetting($this->_getParam('class'), 'showVisible')) {
            $this->_columns->add(new Vps_Grid_Column_Visible());
        }
    }

    protected function _beforeInsert($row)
    {
        if (is_null($row->visible)) $row->visible = 0;
    }
}
