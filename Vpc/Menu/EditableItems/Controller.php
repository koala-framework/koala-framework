<?php
class Vpc_Menu_EditableItems_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array();
    protected $_model = 'Vpc_Menu_EditableItems_Model';
    protected $_defaultOrder = array('field' => 'pos', 'direction' => 'ASC');

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('pos'));
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Page name'), 200));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('parent_component_id', $this->_getParam('componentId'));
        $ret->whereEquals('ignore_visible', true);
        return $ret;
    }
}
