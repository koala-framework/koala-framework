<?php
class Vpc_Basic_LinkTag_Event_EventsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vpc_Events_Directory_Model';
    protected $_defaultOrder = array('field'=>'start_date', 'direction'=>'DESC');
    protected $_paging = 20;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('title'));
        $this->_columns->add(new Vps_Grid_Column('start_date'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('visible', 1);
        $ret->whereEquals('component_id', $this->_getParam('eventsComponentId'));
        return $ret;
    }
}
