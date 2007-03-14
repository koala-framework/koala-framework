<?php
abstract class E3_Component_Abstract
{
    protected $_dao;
    protected $_componentId;
    private $_hasGenerated = false;

    public function __construct($componentId, E3_Dao $dao)
    {
        $this->_dao = $dao;
        $this->_componentId = $componentId;
    }

    public final function callGenerateHierarchy(E3_PageCollection_Abstract $pageCollection)
    {
		if (!$this->_hasGenerated) {
			$this->generateHierarchy($pageCollection);
			$this->_hasGenerated = true;
		}    	
    }
    
    protected function generateHierarchy(E3_PageCollection_Abstract $pageCollection)
    {
        $componentModel = $this->_dao->getTable('E3_Dao_Components');
        $rows = $this->_dao->getTable('E3_Dao_Pages')
                ->fetchChildRowsByComponentId($this->getComponentId());

        foreach($rows as $pageRow) {
            $componentClass = $componentModel->getComponentClass($pageRow->componentId);
            $component = new $componentClass($pageRow->componentId, $this->getDao());
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
