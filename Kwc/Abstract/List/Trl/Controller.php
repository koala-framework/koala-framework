<?php
class Kwc_Abstract_List_Trl_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array('save');
    protected $_model = 'Kwc_Abstract_List_Trl_AdminModel';
    protected $_sortable = false;
    protected $_defaultOrder = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Visible());

        $c = Kwc_Abstract::getChildComponentClass($this->_getParam('class'), 'child');
        foreach (Kwc_Admin::getInstance($c)->gridColumns() as $i) {
            $this->_columns->add($i);
        }
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_getModel()->setComponentId($this->_getParam('componentId'));
    }
}
