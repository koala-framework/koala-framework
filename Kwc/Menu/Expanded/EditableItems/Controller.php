<?php
class Kwc_Menu_Expanded_EditableItems_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array();
    protected $_model = 'Kwc_Menu_Expanded_EditableItems_Model';
    protected $_sortable = false;
    protected $_grouping = array(
        'groupField' => 'parent_name',
        'noGroupSummary' => true,
        'remoteGroup' => true
    );

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('pos'));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Page name'), 150));
        $this->_columns->add(new Kwf_Grid_Column('parent_name', trlKwf('Parent Page Name')))
            ->setHidden(true);
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('parent_component_id', $this->_getParam('componentId'));
        $ret->whereEquals('ignore_visible', true);
        $ret->order('parent_pos', 'ASC');
        $ret->order('pos', 'ASC');
        return $ret;
    }
}
