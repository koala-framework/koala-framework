<?php
class E3_Component_Decorator_Abstract implements E3_Component_Interface
{
    protected $_component;
    protected $_dao;

    public function __construct(E3_Dao $dao, E3_Component_Interface $component)
    {
        $this->_dao = $dao;
        $this->_component = $component;
    }
    
    public function getTemplateVars()
    {
        return $this->_component->getTemplateVars();
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
    public final function generateHierarchy(E3_PageCollection_Abstract $pageCollection, $filename='')
    {
        return $this->_component->generateHierarchy($pageCollection, $filename);
    }
    public function generateTreeHierarchy(E3_PageCollection_Tree $pageCollection, $filename)
    {
        return $this->_component->generateTreeHierarchy($pageCollection, $filename);
    }

}
