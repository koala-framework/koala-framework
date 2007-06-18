<?php
abstract class Vpc_Abstract implements Vpc_Interface
{
    protected $_dao;
    private $_componentId;
    private $_componentKey;
    private $_pageKey;
    private $_hasGeneratedForFilename = array();
    private $_pageCollection = null;
    private $_pageId = null;

    public function __construct(Vps_Dao $dao, $componentId, $pageKey='', $componentKey='')
    {
        $this->_dao = $dao;
        $this->_componentId = (int)$componentId;
        $this->_pageKey = $pageKey;
        $this->_componentKey = $componentKey;
        $this->setup();
    }
    
    public function setPageId($pageId)
    {
        $this->_pageId = (string)$pageId;
    }
    
    public function getPageId($ignorePageKey = false)
    {
        if (!$this->_pageId) {
            $this->setPageId($this->getId());
        }
        $pageId = (string)$this->_pageId;
        if (!$ignorePageKey && $this->getPageKey() != '') {
            $pageId .= '_' . $this->getPageKey();
        }
        return $pageId;
    }
    
    protected function setup() {}

    public static function getInstance($dao, $id, $className = '', $pageKey = '', $componentKey = '')
    {
        $parsedId = Vpc_Abstract::parseId($id);
        $componentId = $parsedId['componentId'];

        if ($className == '') {
            $data = $dao->getTable('Vps_Dao_Pages')->retrievePageData($componentId);
            if ($data) {
                $className = $data['component'];
            }
        }
        
        // Komponente erstellen
        try {
            
            if (class_exists($className)) {
                $component = new $className($dao, $componentId, $pageKey, $componentKey);
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
        $pages = array();
        if ($this->_pageCollection instanceof Vps_PageCollection_Tree) {
            // Erstellt hier nur ChildPages, ParentPages werden bei bedarf in Vps_PageCollection_Tree erstellt
            if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) {

                // Hierarchie aus Seitenbaum immer erstellen
                $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData($this->getComponentId());
                foreach($rows as $pageRow) {
                    if ($filename != '' && $filename != $pageRow['filename']) { continue; }
                    $component = $this->createComponent($pageRow['component'], $pageRow['component_id']);
                    $component->setPageId($pageRow['id']);
                    $this->_pageCollection->addPage($component, $pageRow['filename']);
                    $this->_pageCollection->setParentPage($component, $this);
                    $pages[$pageRow['filename']] = $component;
                }

                // Hierarchie von aktueller Komponente nur erstellen, wenn die dynamischen Seiten auch angezeigt werden sollen
                if ($this->_pageCollection->getCreateDynamicPages()) {
                    $components = $this->getChildPages($filename);
                    foreach ($components as $fn => $component) {
                        $this->_pageCollection->addPage($component, $fn);
                        $this->_pageCollection->setParentPage($component, $this);
                        $pages[$fn] = $component;
                    }
                }

                $this->_hasGeneratedForFilename[] = $filename;
            }

        } else if ($this->_pageCollection != null){

            throw new Vpc_Exception('Until now, generateHierarchy only works for instances of Vps_PageCollection_Tree');

        }

        return $pages;
    }

    protected function getChildPages($filename = '')
    {
        return array();
    }

    protected function createComponent($className, $componentId = 0, $pageKeySuffix = '', $componentKeySuffix = '')
    {
        // Exceptions
        if ($className == '' && $componentId == 0) {
            throw new Vpc_Exception('Either className or componentId must not be empty.');
        }

        // Falls neue componentId, nach Decoratorn suchen (werden unten hinzugefügt)
        $decoratorData = array();
        if ($componentId > 0) {
            $decoratorData = $this->_dao->getTable('Vps_Dao_Pages')->retrieveDecoratorData($componentId);
        }

        // Benötige Daten ggf. holen
        if ($componentId == 0) {
            $componentId = $this->getComponentId();
        }

        // Keys holen
        $pageKey = $this->getPageKey();
        if ($pageKey != '' && $pageKeySuffix != '') { $pageKey .= '.'; }
        $pageKey .= $pageKeySuffix;

        $componentKey = $this->getComponentKey();
        if ($componentKey != '' && $componentKeySuffix != '') { $componentKey .= '_'; }
        $componentKey .= $componentKeySuffix;

        // Komponente erstellen
        $component = Vpc_Abstract::getInstance($this->getDao(), $componentId, $className, $pageKey, $componentKey);
        $component->setPageId($this->getPageId(true));

        // Zu Komponente ggf. PageCollection hinzufügen
        if (!is_null($component) && !is_null($this->_pageCollection)) {
            $component->setPageCollection($this->_pageCollection);
        }
        
        // Erstellte Komponente hinzufügen
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

    protected function getPath()
    {
        return $this->_pageCollection->getPath($this);
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
            throw new Vpc_Exception("ID $id doesn't match pattern for pageId: $pattern");
        }

        $parts['id'] = $keys[0];
        $parts['componentId'] = (int)$keys[1];
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

    public function getChildComponents()
    {
        return array();
    }
}
