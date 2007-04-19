<?php
abstract class Vps_Component_Decorator_Abstract implements Vps_Component_Interface
{
    protected $_component;
    protected $_dao;

    public function __construct(Vps_Dao $dao, Vps_Component_Interface $component)
    {
        $this->_dao = $dao;
        $this->_component = $component;
    }
    
    public function getTemplateVars($mode)
    {
        return $this->_component->getTemplateVars($mode);
    }
    
    public function getId()
    {
        return $this->_component->getId();
    }
    
    public function getComponentInfo()
    {
        return $this->_component->getComponentInfo();
    }

    protected function getDao()
    {
        return $this->_dao;
    }
    public final function generateHierarchy(Vps_PageCollection_Abstract $pageCollection, $filename='')
    {
        return $this->_component->generateHierarchy($pageCollection, $filename);
    }
    public function generateTreeHierarchy(Vps_PageCollection_Tree $pageCollection, $filename)
    {
        return $this->_component->generateTreeHierarchy($pageCollection, $filename);
    }
    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        return $this->_component->saveFrontendEditing($request);
    }
}
