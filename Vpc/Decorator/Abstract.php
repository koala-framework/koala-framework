<?php
/**
 * Basisklasse für Decorators
 * @package Vpc
 * @subpackage Decorator
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Decorator_Abstract implements Vpc_Interface
{
    protected $_component;
    protected $_dao;
    protected $_pageCollection;

    /**
     * Ein Decorator kann im Gegensatz zu einer Komponenten direkt im
     * Konstruktor erstellt werden, da die Eigenschaften der Komponente
     * ohnehin durchgeschleift werden.
     * 
     * @param Vps_Dao DAO
     * @param Vpc_Interface Komponente, die dekoriert werden soll
     */
    public function __construct(Vps_Dao $dao, Vpc_Interface $component)
    {
        $this->_dao = $dao;
        $this->_component = $component;
    }
    
    /**
     * Setzt für sich und für die dekorierte Komponente die pageCollection
     * 
     * @param Vps_PageCollection_Abstract
     */
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection)
    {
        $this->_pageCollection = $pageCollection;
        $this->_component->setPageCollection($pageCollection);
    }

    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getTemplateVars($mode)
    {
        return $this->_component->getTemplateVars($mode);
    }
    
    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getId()
    {
        return $this->_component->getId();
    }
    
    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getPageId()
    {
        return $this->_component->getPageId();
    }
    
    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getComponentInfo()
    {
        return $this->_component->getComponentInfo();
    }

    /**
     * @return Vps_Dao DAO
     */
    protected function getDao()
    {
        return $this->_dao;
    }

    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function generateHierarchy($filename = '')
    {
        return $this->_component->generateHierarchy($filename);
    }

    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        return $this->_component->saveFrontendEditing($request);
    }
    
    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getChildComponents()
    {
        return array($this->_component);
    }
    
    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function findComponent($id)
    {
        return $this->_component->findComponent($id);
    }
    
    /**
     * Schleift die Methode auf auf dekorierte Komponente durch, findet
     * aber auch den Decorator selbst.
     */
    public function findComponentByClass($class)
    {
        if (get_class($this) == $class) {
            return $this;
        } else {
            return $this->_component->findComponentByClass($class);
        }
    }

}
