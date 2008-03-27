<?php
class Vpc_Abstract_List_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_position = 'pos';
    protected $_buttons = array('save', 'add', 'delete');

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column_Visible());
    }

    protected function _beforeInsert($row)
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');
        $row->visible = 0;
        $row->component_class = $classes['child'];
    }
}
