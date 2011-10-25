<?php
class Kwc_Menu_EditableItems_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array();
    protected $_model = 'Kwc_Menu_EditableItems_Model';
    protected $_defaultOrder = array('field' => 'pos', 'direction' => 'ASC');

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('pos'));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Page name'), 200));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('parent_component_id', $this->_getParam('componentId'));
        $ret->whereEquals('ignore_visible', true);
        return $ret;
    }
}
