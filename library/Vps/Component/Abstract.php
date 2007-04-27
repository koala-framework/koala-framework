<?php
abstract class Vps_Component_Abstract implements Vps_Component_Interface
{
    protected $_dao;
    private $_componentId;
    private $_componentKey;
    private $_pageKey;
    private $_hasGeneratedForFilename = array();
    private $_pageCollection = null;

    public function __construct(Vps_Dao $dao, $componentId, $pageKey='', $componentKey='')
    {
        $this->_dao = $dao;
        $this->_componentId = (int)$componentId;
        $this->_pageKey = $pageKey;
        $this->_componentKey = $componentKey;
        $this->setup();
    }
    
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection)
    {
        $this->_pageCollection = $pageCollection;
    }
    
    protected function setup()
    {
    }

    public final function generateHierarchy($filename = '')
    {
        $pages = array();
        if ($this->_pageCollection instanceof Vps_PageCollection_Tree) {
            // Erstellt hier nur ChildPages, ParentPages werden bei bedarf in Vps_PageCollection_Tree erstellt
            if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) {

                // Hierarchie aus Seitenbaum immer erstellen
                $rows = $this->_dao->getChildPagesData($this->getComponentId());
                foreach($rows as $pageRow) {
                    if ($filename != '' && $filename != $pageRow['filename']) { continue; }
                    $component = $this->createComponent($pageRow['component'], $pageRow['component_id']);
                    $this->_pageCollection->addPage($component, $pageRow['filename']);
                    $this->_pageCollection->setParentPage($component, $this);
                    $pages[$pageRow['filename']] = $component;
                }

                // Hierarchie von aktueller Komponente nur erstellen, wenn die dynamischen Seiten auch angezeigt werden sollen
                if ($this->_pageCollection->getCreateDynamicPages()) {
                    $components = $this->createComponents($filename);
                    foreach ($components as $fn => $component) {
                        $this->_pageCollection->addPage($component, $fn);
                        $this->_pageCollection->setParentPage($component, $this);
                        $pages[$fn] = $component;
                    }
                }

                $this->_hasGeneratedForFilename[] = $filename;
            }

        } else if ($this->_pageCollection != null){

            throw new Vps_Component_Exception('Until now, generateHierarchy only works for instances of Vps_PageCollection_Tree');

        }
        
        return $pages;
    }
    
    protected function createComponents($filename) {
        return array();
    }

    protected function createComponent($className, $componentId = 0, $pageKeySuffix = '', $componentKeySuffix = '')
    {
        if ($className == '' && $componentId == 0) {
            throw new Vps_Component_Exception('Either className or componentId must not be empty.');
        }
        
        if ($className == '') {
            $data = $this->_dao->getPageData($componentId);
            $className = $data['component'];
        } else if ($componentId == 0) {
            $componentId = $this->getComponentId();
        }

        $pageKey = $this->getPageKey();
        if ($pageKey != '' && $pageKeySuffix != '') { $pageKey .= '.'; }
        $pageKey .= $pageKeySuffix;


        $componentKey = $this->getComponentKey();
        if ($componentKey != '' && $componentKeySuffix != '') { $componentKey .= '.'; }
        $componentKey .= $componentKeySuffix;

        $component = new $className($this->getDao(), $componentId, $pageKey, $componentKey);
        if (!is_null($this->_pageCollection)) {
            $component->setPageCollection($this->_pageCollection);
        }
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
