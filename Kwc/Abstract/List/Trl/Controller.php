<?php
class Vpc_Abstract_List_Trl_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array('save');
    protected $_model = 'Vpc_Abstract_List_Trl_AdminModel';
    protected $_sortable = false;
    protected $_defaultOrder = 'pos';

    protected function _initColumns()
    {
        $c = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
        foreach (Vpc_Admin::getInstance($c)->gridColumns() as $i) {
            $this->_columns->add($i);
        }

        $this->_columns->add(new Vps_Grid_Column_Visible());
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_getModel()->setComponentId($this->_getParam('componentId'));
    }
}
