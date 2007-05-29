<?php
abstract class Vps_Component_Decorator_Abstract implements Vps_Component_Interface
{
    protected $_component;
    protected $_dao;
    protected $_pageCollection;

    public function __construct(Vps_Dao $dao, Vps_Component_Interface $component)
    {
        $this->_dao = $dao;
        $this->_component = $component;
    }
    
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection)
    {
        $this->_pageCollection = $pageCollection;
        $this->_component->setPageCollection($pageCollection);
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

    public function generateHierarchy($filename = '')
    {
        return $this->_component->generateHierarchy($filename);
    }

    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        return $this->_component->saveFrontendEditing($request);
    }
    
    public function getChildComponents()
    {
        return array($this->_component);
    }
    
    public function findComponent($id, $findDecorators = false)
    {
        if ($findDecorators && $id == $this->getId()) {
            return $this;
        } else {
            return $this->_component->findComponent($id, $findDecorators);
        }
    }
}
