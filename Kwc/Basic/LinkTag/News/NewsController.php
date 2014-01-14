<?php
class Kwc_Basic_LinkTag_News_NewsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_permissions = array();
    protected $_modelName = 'Kwc_News_Directory_Model';
    protected $_defaultOrder = array('field'=>'publish_date', 'direction'=>'DESC');
    protected $_paging = 20;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('title'));
        $this->_columns->add(new Kwf_Grid_Column('publish_date'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('newsComponentId'));
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        $componentId = $this->_getParam('newsComponentId');
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible'=>true, 'limit'=>1));
        if (!$component) return false;
        if (!is_instance_of($component->componentClass, 'Kwc_News_Directory_Component')) return false;
        $subRoot = $component->getSubroot();
        return Kwf_Registry::get('acl')->getComponentAcl()->isAllowed($this->_getAuthData(), $subRoot);
    }
}
