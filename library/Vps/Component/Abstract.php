<?php
abstract class Vps_Component_Abstract implements Vps_Component_Interface
{
    protected $_dao;
    private $_componentId;
    private $_componentKey;
    private $_pageKey;
    private $_hasGeneratedForFilename = array();

    public function __construct(Vps_Dao $dao, $componentId, $pageKey='', $componentKey='')
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

    public final function generateHierarchy(Vps_PageCollection_Abstract $pageCollection, $filename = '', $createDynamicPages = true)
    {
        if ($pageCollection instanceof Vps_PageCollection_Tree) {

            if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) {
                
                // Hierarchie aus Seitenbaum immer erstellen
                $rows = $this->_dao->getTable('Vps_Dao_Pages')->fetchChildRows($this->getComponentId(), $filename);
                foreach($rows as $pageRow) {
                    $this->createPageInTree($pageCollection, false, $pageRow->filename, $pageRow->component_id);
                }
                
                // Hierarchie von aktueller Komponente nur erstellen, wenn die dynamischen Seiten auch angezeigt werden sollen
                if ($pageCollection->getCreateDynamicPages()) {
                    $this->generateTreeHierarchy($pageCollection, $filename);
                }
                
                $this->_hasGeneratedForFilename[] = $filename;
            }
            
        } else {
            
            throw new Vps_Component_Exception('Until now, generateHierarchy only works for instances of Vps_PageCollection_Tree');
            
        }
    }

    protected function generateTreeHierarchy(Vps_PageCollection_Tree $pageCollection, $filename) {}

    protected function createPageInTree(Vps_PageCollection_Tree $pageCollection, $className, $filename, $componentId, $postfixKey = '')
    {
        $key = '';
        if ($this->getPageKey() != '') { $key = $this->getPageKey() . '.'; }
        $key = $key . $postfixKey;
        if (!$className) {
            $className = $this->_dao->getTable('Vps_Dao_Components')->getComponentClass($componentId);
        }
        
        $component = new $className($this->getDao(), $componentId, $key, $this->getComponentKey());
        $pageCollection->addPage($component, $filename);
        $pageCollection->setParentPage($component, $this);

        return $component;
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
        if ($this->getPageKey() != '') {
            $ret .= '_' . $this->getPageKey();
        }
        if ($this->getComponentKey() != '') {
            $ret .= '-' . $this->getComponentKey();
        }
        return $ret;
    }
    
    public static function parseId($id)
    {
        $keys = array();
        $pattern  = '^';
        $pattern .= '(\d+)';
        $pattern .= '(_\d+(\.\d+)*)?';
        $pattern .= '(-\d+(\.\d+)*)?';
        $pattern .= '$';
        preg_match("#$pattern#", $id, $keys);

        if ($keys == null) {
            throw new Vps_Component_Exception("ID $id doesn't match pattern for pageId: $pattern");
        }

        $parts['id'] = $keys[0];
        $parts['componentId'] = $keys[1];
        $parts['pageKey'] = isset($keys[2]) ? substr($keys[2], 1) : '';
        $parts['componentKey'] = isset($keys[4]) ? substr($keys[4], 1) : '';
        $parts['pageKeys'] = $parts['pageKey'] != '' ? explode('.', $parts['pageKey']) : array();
        $parts['componentKeys'] = $parts['componentKey'] != '' ? explode('.', $parts['componentKey']) : array();
        return $parts;
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

    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        return array();
    }
}
