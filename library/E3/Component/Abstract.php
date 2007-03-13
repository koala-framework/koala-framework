<?php
abstract class E3_Component_Abstract
{
    protected $_dao;
    protected $_componentId;

    public function __construct($componentId, $dao)
    {
        $this->_dao = $dao;
        $this->_componentId = $componentId;
    }

    public function generateHierachy(E3_PageCollection_Abstract $pageCollection)
    {
        $componentModel = $this->_dao->getModel('E3_Model_Components');
        $rows = $this->_dao->getModel('E3_Model_Pages')
                ->fetchChildRowsByComponentId($this->getComponentId());

        foreach($rows as $pageRow) {
            $componentClass = $componentModel->getComponentClass($pageRow->componentId);
            $component = new $componentClass($pageRow->componentId, $this->getDao());
            $page = $pageCollection->addPage($component, $pageRow->filename, $pageRow->componentId);
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
