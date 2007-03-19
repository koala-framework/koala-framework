<?php
abstract class E3_Component_Abstract
{
    protected $_dao;
    protected $_componentId;
    private $_hasGeneratedForFilename = array();
    private $_generatedIds = array();

    public function __construct($componentId, E3_Dao $dao)
    {
        $this->_dao = $dao;
        $this->_componentId = (int)$componentId;
    }

    public final function callGenerateHierarchy(E3_PageCollection_Abstract $pageCollection, $filename='')
    {
        //fixme: noch nicht optimal, wenn zuerst mit filename dann ohne aufgerufen werden componenten doppelt erstellt
        if (!in_array($filename, $this->_hasGeneratedForFilename)) {
            $this->generateHierarchy($pageCollection, $filename);
            $this->_hasGeneratedForFilename[] = $filename;
        }
    }
    
    protected function generateHierarchy(E3_PageCollection_Abstract $pageCollection, $filename)
    {
        $componentModel = $this->_dao->getTable('E3_Dao_Components');
        $rows = $this->_dao->getTable('E3_Dao_Pages')
                ->fetchChildRows($this->getComponentId(), $filename);

        foreach($rows as $pageRow) {
            if(in_array($pageRow->component_id, $this->_generatedIds)) continue; //fixme: uneffizient, unnötiger speicherverbrauch, langsam
            $this->_generatedIds[] = $pageRow->component_id;
            $componentClass = $componentModel->getComponentClass($pageRow->component_id);
            $component = new $componentClass($pageRow->component_id, $this->getDao());
            $page = $pageCollection->addPage($component, $pageRow->filename);
            $pageCollection->setParentPage($component, $this);
        }
    }

    public function getComponentId()
    {
        return $this->_componentId;
    }
    public function getTemplateVars()
    {
        return array();
    }
    protected function getDao()
    {
        return $this->_dao;
    }
}
