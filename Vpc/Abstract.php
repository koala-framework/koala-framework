<?php
abstract class Vpc_Abstract implements Vpc_Interface
{
    protected $_dao;
    private $_topComponentId;
    private $_componentId;
    protected $_pageKey;
    private $_componentKey;
    private $_hasGeneratedForFilename = array();
    private $_pageCollection = null;

    protected final function __construct(Vps_Dao $dao, $topComponentId, $componentId, $pageKey = '', $componentKey = '')
    {
        $this->_dao = $dao;
        $this->_topComponentId = (int)$topComponentId;
        $this->_componentId = (int)$componentId;
        $this->_pageKey = $pageKey;
        $this->_componentKey = $componentKey;
        $this->setup();
    }
    
    protected function setup() {}

    public static function createInstance(Vps_Dao $dao, $id)
    {
        $parsedId = self::parseId($id);
        return self::_createInstance($dao, $parsedId['componentId'], $parsedId['componentId'], $parsedId['pageKey'], '', $parsedId['componentKey']);
    }
    
    protected function createPage($className, $componentId = 0, $pageKeySuffix = '', $pageTagSuffix = '')
    {
        // Benötige Daten ggf. holen
        if ($componentId == 0) {
            $componentId = $this->getTopComponentId();
        }

        if ($pageKeySuffix != '' && $pageTagSuffix != '') {
            throw new Vpc_Exception('Only one of $pageKeySuffix and $pageTagSuffix can have a value.');
        }

        $pageKey = $this->_pageKey;
        if ($pageKeySuffix != '') {
            if ($pageKey != '') { $pageKey .= '.'; }
            $pageKey .= $pageKeySuffix;
        }
        if ($pageTagSuffix != '') {
            if ($pageKey != '') { $pageKey .= '.'; }
            $pageKey .= $pageTagSuffix . 't';
        }

        // Page erstellen
        $page = self::_createInstance($this->getDao(), $componentId, $componentId, $pageKey, '', $className);
        
        // Zu Komponente ggf. PageCollection hinzufügen
        if (!is_null($page) && !is_null($this->_pageCollection)) {
            $page->setPageCollection($this->_pageCollection);
        }
        
        // Erstellte Komponente hinzufügen
        return $page;
    }
    
    protected function createComponent($className, $componentId = 0, $componentKeySuffix = '')
    {
        // Benötige Daten ggf. holen
        if ($componentId == 0) {
            $componentId = $this->getComponentId();
        }

        $componentKey = $this->getComponentKey();
        if ($componentKey != '' && $componentKeySuffix != '') { $componentKey .= '_'; }
        $componentKey .= $componentKeySuffix;

        // Komponente erstellen
        $component = self::_createInstance($this->getDao(), $this->getTopComponentId(), $componentId, $this->_pageKey, $componentKey, $className);

        // Zu Komponente ggf. PageCollection hinzufügen
        if (!is_null($component) && !is_null($this->_pageCollection)) {
            $component->setPageCollection($this->_pageCollection);
        }
        
        // Erstellte Komponente hinzufügen
        return $component;
    }

    private static function _createInstance(Vps_Dao $dao, $topComponentId, $componentId, $pageKey = '', $componentKey = '', $className = '')
    {
        if ($className == '') {
            $data = $dao->getTable('Vps_Dao_Pages')->retrievePageData($componentId);
            if ($data) {
                $className = $data['component'];
            }
        }
        
        // Komponente erstellen
        try {
            
            if (class_exists($className)) {
                $component = new $className($dao, $topComponentId, $componentId, $pageKey, $componentKey);
            }

            // Decorators hinzufügen
            if (!is_null($component)) {
                $decoratorData = $dao->getTable('Vps_Dao_Pages')->retrieveDecoratorData($component->getId());
                foreach ($decoratorData as $className) {
                    if (class_exists($className)) {
                        $component = new $className($dao, $component);
                    }
                }
            }

        } catch (Zend_Exception $e) {
            throw new Vpc_ComponentNotFoundException("Component '$className' not found.");
        }

        return $component;
    }
    
    public static function parseId($id)
    {
        $keys = array();
        $pattern  = '^';
        $pattern .= '(\d+)'; // ComponentId
        $pattern .= '(_\d+(\.\d+)*)?'; // PageKey
        $pattern .= '(-\d+(\.\d+)*)?'; // ComponentKey
        $pattern .= '$';
        preg_match("#$pattern#", $id, $keys);

        if ($keys == null) {
            throw new Vpc_Exception("ID $id doesn't match pattern for Id: $pattern");
        }

        $parts['id'] = $keys[0];
        $parts['componentId'] = (int)$keys[1];
        $parts['pageKey'] = isset($keys[2]) ? substr($keys[2], 1) : '';
        $parts['componentKey'] = isset($keys[4]) ? substr($keys[4], 1) : '';
        $parts['pageKeys'] = $parts['pageKey'] != '' ? explode('.', $parts['pageKey']) : array();
        $parts['componentKeys'] = $parts['componentKey'] != '' ? explode('.', $parts['componentKey']) : array();
        return $parts;
    }

    public static function getPageIdPattern()
    {
        $pattern = '(\d+)'; // TopComponentId
        $pattern .= '(_\d+t?(\.\d+t?)*)?'; // PageKey
        return $pattern;
    }

    public static function parsePageId($id)
    {
        $keys = array();
        preg_match('#^' . self::getPageIdPattern() . '$#', $id, $keys);

        if ($keys == null) {
            throw new Vpc_Exception("ID $id doesn't match pattern for pageId: $pattern");
        }

        $parts['id'] = $keys[0];
        $parts['topComponentId'] = (int)$keys[1];
        $parts['pageKey'] = isset($keys[2]) && $keys[2] != '' ? substr($keys[2], 1) : '';
        $parts['pageKeys'] = $parts['pageKey'] != '' ? explode('.', $parts['pageKey']) : array();
        return $parts;
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

    public function getPageId()
    {
        $pageId = (string)$this->_topComponentId;
        if ($this->_pageKey != '') {
            $pageId .= '_' . $this->_pageKey;
        }
        return $pageId;
    }
    
    protected function getComponentId()
    {
        return (int)$this->_componentId;
    }
    
    protected function getTopComponentId()
    {
        return (int)$this->_topComponentId;
    }
    
    protected function getComponentKey()
    {
        return $this->_componentKey;
    }
    
    protected function getPageKey()
    {
        $parts = array();
        foreach (explode('.', $this->_pageKey) as $part) {
            if (substr($part, -1) != 't') {
                $parts[] = $part;
            }
        }
        return implode('.', $parts);
    }
    
    protected function getPageTag()
    {
        $parts = array();
        foreach (explode('.', $this->_pageKey) as $part) {
            if (substr($part, -1) == 't') {
                $parts[] = substr($part, 0, -1);
            }
        }
        return implode('.', $parts);
    }

    protected function getCurrentPageKey()
    {
        $pageKeys = explode('.', $this->getPageKey());
        return array_pop($pageKeys);
    }
    
    protected function getCurrentPageTag()
    {
        $pageKeys = explode('.', $this->getPageTag());
        return array_pop($pageKeys);
    }

    public function findComponent($id, $findDecorators = false)
    {
        if ($this->getId() == $id) {
            return $this;
        } else {
            foreach ($this->getChildComponents() as $childComponent) {
                $component = $childComponent->findComponent($id, $findDecorators);
                if ($component != null) {
                    return $component;
                }
            }
        }
        return null;
    }

    public function findComponentByClass($class)
    {
        if (get_class($this) == $class) {
            return $this;
        } else {
            foreach ($this->getChildComponents() as $childComponent) {
                $component = $childComponent->findComponentByClass($class);
                if ($component != null) {
                    return $component;
                }
            }
        }
        return null;
    }

    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection)
    {
        $this->_pageCollection = $pageCollection;
    }

    protected function getPageCollection()
    {
        return $this->_pageCollection;
    }

    public final function generateHierarchy($filename = '')
    {
        $return = array();
        if ($this->_pageCollection instanceof Vps_PageCollection_Tree) {
            // Erstellt hier nur ChildPages, ParentPages werden bei bedarf in Vps_PageCollection_Tree erstellt
            if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) {

                // Hierarchie aus Seitenbaum immer erstellen
                $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData($this->getComponentId());
                foreach($rows as $pageRow) {
                    if ($filename != '' && $filename != $pageRow['filename']) { continue; }
                    $page = $this->createPage('', $pageRow['component_id']);
                    $this->_pageCollection->addPage($page, $pageRow['filename']);
                    $this->_pageCollection->setParentPage($page, $this);
                    $return[$pageRow['filename']] = $page;
                }

                // Hierarchie von aktueller Komponente nur erstellen, wenn die dynamischen Seiten auch angezeigt werden sollen
                if ($this->_pageCollection->getCreateDynamicPages()) {
                    $pages = $this->getChildPages($filename);
                    foreach ($pages as $fn => $page) {
                        $this->_pageCollection->addPage($page, $fn);
                        $this->_pageCollection->setParentPage($page, $this);
                        $return[$fn] = $page;
                    }
                }

                $this->_hasGeneratedForFilename[] = $filename;
            }

        } else if ($this->_pageCollection != null){

            throw new Vpc_Exception('Until now, generateHierarchy only works for instances of Vps_PageCollection_Tree');

        }

        return $return;
    }

    protected function getChildPages($filename = '')
    {
        return array();
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

    public function getChildComponents()
    {
        return array();
    }
    
    protected function getPath($component = null)
    {
        if ($component == null) { $component = $this; }
        return $this->getPageCollection()->getPath($component);
    }
    
}
