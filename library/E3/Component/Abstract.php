<?php
abstract class E3_Component_Abstract implements E3_Component_Interface
{
    protected $_dao;
    private $_componentId;
    private $_componentKey;
    private $_pageKey;
    private $_hasGeneratedForFilename = array();

    public function __construct(E3_Dao $dao, $componentId, $pageKey="", $componentKey="")
    {
        $this->_dao = $dao;
        $this->_componentId = (int)$componentId;
        $this->_pageKey = $pageKey;
        $this->_componentKey = $componentKey;
        $this->setup();
    }

    protected function setup()
    {
    }
/*
    public static function createComponent($dao, $componentId, $pageKey="", $componentKey="")
    {
        $model = $dao->getTable('E3_Dao_Components');
        $class = $model->getComponentClass($componentId);
      return new $class($dao, $componentId, $pageKey, $componentKey);
    }*/

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
        $rows = $this->_dao->getTable('E3_Dao_Pages')
                ->fetchChildRows($this->getComponentId(), $filename);

        foreach($rows as $pageRow) {
            $id = (int)$pageRow->component_id;
            $this->createPageInTree($pageCollection, false, $pageRow->filename, $pageRow->component_id);
        }
    }

    protected function createPageInTree(E3_PageCollection_Tree $pageCollection, $className, $filename, $componentId, $postfixKey = '')
    {
         $key = "";
        if ($this->getPageKey() != "") $key = $this->getPageKey() . ".";
        $key = $key . $postfixKey;
        if (!$className) {
            $className = $this->_dao->getTable('E3_Dao_Components')->getComponentClass($componentId);
        }
        if (!$pageCollection->pageExists($componentId, $key)) {
            $component = new $className($this->getDao(), $componentId, $key, $this->getComponentKey());

        $pageCollection->addPage($component, $filename);
          $pageCollection->setParentPage($component, $this);

          return $component;
        }
        return null;

    }

    protected function getComponentId()
    {
        return (int)$this->_componentId;
    }
    protected function getComponentKey()
    {
        return $this->_componentKey;
    }
    protected function getPageKey()
    {
        return $this->_pageKey;
    }

    public function getId()
    {
        $ret = (string)$this->getComponentId();
        if ($this->getPageKey() != "") {
            $ret .= "_" . $this->getPageKey();
        }
        if ($this->getComponentKey() != "") {
            $ret .= "|" . $this->getComponentKey();
        }
        return $ret;
    }
    public function getTemplateVars($mode)
    {
        $ret['id'] = $this->getId();
        return $ret;
    }

    public function getComponentInfo()
    {
        return array($this->getId() => get_class($this));
    }

    protected function getDao()
    {
        return $this->_dao;
    }
}
