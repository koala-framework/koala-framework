<?php
abstract class E3_Component_Abstract
{
    protected $_dao;
    protected $_componentId;
    private $_hasGeneratedForFilename = array();

    public function __construct($componentId, E3_Dao $dao)
    {
        $this->_dao = $dao;
        $this->_componentId = (int)$componentId;
    }
    
    public static function createComponent($dao, $componentId)
    {
        $model = $dao->getTable('E3_Dao_Components');
        $class = $model->getComponentClass($componentId);
	    return new $class($componentId, $dao);
    }

    public final function generateHierarchy(E3_PageCollection_Abstract $pageCollection, $filename='')
    {
        if ($pageCollection instanceof E3_PageCollection_Tree) {
	        if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) {
	            $this->generateTreeHierarchy($pageCollection, $filename);
	            $this->_hasGeneratedForFilename[] = $filename;
	        }
        } else {
        	throw new E3_Component_Exception('Until now, generateHierarchy only works for instances of E3_PageCollection_Tree');
        }
    }
    
    protected function generateTreeHierarchy(E3_PageCollection_Tree $pageCollection, $filename)
    {
        $componentModel = $this->_dao->getTable('E3_Dao_Components');
        $rows = $this->_dao->getTable('E3_Dao_Pages')
                ->fetchChildRows($this->getComponentId(), $filename);

        foreach($rows as $pageRow) {
            $id = (int)$pageRow->component_id;
            if (!$pageCollection->pageExists($id)) {
	            $componentClass = $componentModel->getComponentClass($pageRow->component_id);
	            $component = new $componentClass($pageRow->component_id, $this->getDao());
	            $pageCollection->addPage($component, $pageRow->filename);
	            $pageCollection->setParentPage($component, $this);
            }
        }
    }

    public function getComponentId()
    {
        return (int)$this->_componentId;
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
