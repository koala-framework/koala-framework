<?php
class Kwc_Basic_LinkTag_Event_EventsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_permissions = array();
    protected $_modelName = 'Kwc_Events_Directory_Model';
    protected $_defaultOrder = array('field'=>'start_date', 'direction'=>'DESC');
    protected $_paging = 20;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('title'));
        $this->_columns->add(new Kwf_Grid_Column('start_date'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('visible', 1);
        $ret->whereEquals('component_id', $this->_getParam('eventsComponentId'));
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        $componentId = $this->_getParam('eventsComponentId');
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible'=>true, 'limit'=>1));
        if (!$component) return false;
        if (!is_instance_of($component->componentClass, 'Kwc_Events_Directory_Component')) return false;
        $subRoot = $component->getSubroot();
        return Kwf_Registry::get('acl')->getComponentAcl()->isAllowed($this->_getAuthData(), $subRoot);
    }
}
