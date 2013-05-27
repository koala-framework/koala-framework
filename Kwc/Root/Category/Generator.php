<?php
class Kwc_Root_Category_Generator extends Kwf_Component_Generator_Abstract
{
    protected $_componentClass = 'row';
    protected $_idSeparator = false;
    protected $_loadTableFromComponent = false;
    protected $_inherits = true;

    protected $_useMobileBreakpoints = NULL;

    protected $_pageDataCache = array();

    private $_basesCache = array();
    protected $_eventsClass = 'Kwc_Root_Category_GeneratorEvents';

    protected function _init()
    {
        parent::_init();
        if (is_null($this->_useMobileBreakpoints)) {
            $this->_useMobileBreakpoints = Kwf_Config::getValue('kwc.mobileBreakpoints');
        }
    }

    private function _getPageData($id)
    {
        if (!isset($this->_pageDataCache[$id])) {
            $ret = $this->_getModel()->fetchColumnsByPrimaryId(array('id', 'is_home', 'name', 'filename', 'visible', 'component', 'hide', 'custom_filename', 'parent_id'), $id);
            if ($ret['is_home']) $ret['visible'] = 1;
            $this->_pageDataCache[$id] = $ret;
        }
        return $this->_pageDataCache[$id];
    }
    
    private function _getChildPageIds($parentId)
    {
        $s = new Kwf_Model_Select();
        if (is_numeric($parentId)) {
            $s->whereEquals('parent_id', $parentId);
        } else {
            $s->where(new Kwf_Model_Select_Expr_Like('parent_id', $parentId.'%'));
        }
        $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
        $pageIds = array();
        foreach ($rows as $row) {
            $pageIds[] = $row['id'];
        }
        return $pageIds;
    }

    /**
     * Returns all recursive children of a page (only visible ones)
     */
    public function getVisiblePageChildIds($parentId)
    {
        return $this->getPageChildIds($parentId, true);
    }

    /**
     * Returns all recursive children of a page
     */
    public function getPageChildIds($parentId, $onlyVisible = false)
    {
        $ret = array();
        /*
        if (!is_numeric(substr($parentId, 0, 1))) {
            foreach ($this->_pageChilds as $pId=>$childs) {
                if (substr($pId, 0, strlen($pId)) == $parentId) {
                    foreach ($childs as $id) {
                        $pd = $this->_getPageData($id);
                        if ($onlyVisible && !$pd['visible']) continue;
                        $ret[] = $id;
                        $ret = array_merge($ret, $this->getPageChildIds($id, $onlyVisible));
                    }
                }
            }
        } else if (isset($this->_pageChilds[$parentId])) {
            foreach ($this->_pageChilds[$parentId] as $id) {
                $pd = $this->_getPageData($id);
                if ($onlyVisible && !$pd['visible']) continue;
                $ret[] = $id;
                $ret = array_merge($ret, $this->getPageChildIds($id, $onlyVisible));
            }
        }
        */
        $ret = $this->_getChildPageIds($parentId);
        foreach ($ret as $i) {
            $ret = array_merge($ret, $this->_getChildPageIds($i));
        }
        return $ret;
    }

    //called by GeneratorEvents when model changes
    public function pageDataChanged()
    {
        //$this->_pageDataLoaded = false;
        $this->_pageDataCache = array();
    }

    protected function _formatSelectFilename(Kwf_Component_Select $select)
    {
        return $select;
    }

    protected function _formatSelectHome(Kwf_Component_Select $select)
    {
        return $select;
    }

    public function getChildIds($parentData, $select = array())
    {
        throw new Kwf_Exception('Not supported yet');
    }

    public function getChildData($parentData, $select = array())
    {
        Kwf_Benchmark::count('GenPage::getChildData');

        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();
        $pageIds = $this->_getPageIds($parentData, $select);

        $ret = array();
        foreach ($pageIds as $pageId) {
            $page = $this->_getPageData($pageId);
            if ($select->hasPart(Kwf_Component_Select::WHERE_SHOW_IN_MENU)) {
                $menu = $select->getPart(Kwf_Component_Select::WHERE_SHOW_IN_MENU);
                if ($menu == $page['hide']) continue;
            }
            if ($select->getPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
            } else if (!Kwf_Component_Data_Root::getShowInvisible()) {
                if (!$page['visible']) continue;
            }
            $d = $this->_createData($parentData, $pageId, $select);
            if ($d) $ret[] = $d;

            if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
                if (count($ret) >= $select->getPart(Kwf_Model_Select::LIMIT_COUNT)) break;
            }
        }
        return $ret;
    }

    protected function _getPageIds($parentData, $select)
    {
        if (!$parentData && ($p = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF))) {
            if ($p->getPage()) $p = $p->getPage();
            $parentData = $p;
        }
        $pageIds = array();

        if ($parentData && !$select->hasPart(Kwf_Component_Select::WHERE_ID)) {
            // diese Abfragen sind implizit recursive=true
            $parentId = $parentData->dbId;
            if ($select->getPart(Kwf_Component_Select::WHERE_HOME)) {

            
                $s = new Kwf_Model_Select();
                $s->whereEquals('is_home', true);
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                $homePages = array();
                foreach ($rows as $row) {
                    $homePages[] = $row['id'];
                }

                //TODO very inefficient; should be done by sql
                foreach ($homePages as $pageId) {
                    $pd = $this->_getPageData($pageId);
                    if (substr($pd['parent_id'], 0, strlen($parentId)) == $parentId) {
                        $pageIds[] = $pageId;
                    } else {
                        $id = $pageId;
                        while (true) {
                            $pd = $this->_getPageData($id);
                            if ($pd['parent_id'] == $parentId) {
                                $pageIds[] = $pageId;
                                break;
                            }
                            $id = $pd['parent_id'];
                            if (!isset($pd[$id])) break;
                        }
                    }
                }
            } else if ($select->hasPart(Kwf_Component_Select::WHERE_FILENAME)) {
                $filename = $select->getPart(Kwf_Component_Select::WHERE_FILENAME);
                $s = new Kwf_Model_Select();
                $s->whereEquals('filename', $filename);
                if (is_numeric($parentId)) {
                    $s->whereEquals('parent_id', $parentId);
                } else {
                    $s->where(new Kwf_Model_Select_Expr_Like('parent_id', $parentId.'%'));
                }
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                foreach ($rows as $row) {
                    $pageIds[] = $row['id'];
                }
                /*
                if (isset($this->_pageFilename[$filename])) {
                    foreach ($this->_pageFilename[$filename] as $pId => $pageId) {
                        if (is_numeric($parentId)) {
                            if ($pId == $parentId) {
                                $pageIds[] = $pageId;
                            }
                        } else {
                            //this is ugly. but we don't get the categories in the parentId
                            if (substr($pId, 0, strlen($parentId)) == $parentId) {
                                $pageIds[] = $pageId;
                            }
                        }
                    }
                }
                */
            } else if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
                $keys = array();
                foreach ($selectClasses as $selectClass) {
                    $key = array_search($selectClass, $this->_settings['component']);
                    if ($key) $keys[] = $key;
                }

                $s = new Kwf_Model_Select();
                $s->whereEquals('component', array_unique($keys));
                if (is_numeric($parentId)) {
                    $s->whereEquals('parent_id', $parentId);
                } else {
                    $s->where(new Kwf_Model_Select_Expr_Like('parent_id', $parentId.'%'));
                }
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                foreach ($rows as $row) {
                    $pageIds[] = $row['id'];
                }
                /*
                foreach (array_unique($keys) as $key) {
                    if (isset($this->_pageComponentParent[$key])) {
                        foreach ($this->_pageComponentParent[$key] as $pId => $ids) {
                            if ($parentId == $pId || substr($pId, 0, strlen($parentId)+1) == $parentId.'-') {
                                $pageIds = array_merge($pageIds, $ids);
                            }
                        }
                    }
                }
                */
            } else {

                $pageIds = $this->_getChildPageIds($parentId);
                /*
                foreach ($this->_pageChilds as $pId => $ids) {
                    if ($parentId == $pId || substr($pId, 0, strlen($parentId)+1) == $parentId.'-') {
                        $pageIds = array_merge($pageIds, $ids);
                    }
                }
                */
            }

        } else {

            if ($select->getPart(Kwf_Component_Select::WHERE_HOME)) {
                //$pageIds = $this->_pageHome;

                //TODO inefficient: dont filter below, instead filter in sql somehow
                $s = new Kwf_Model_Select();
                $s->whereEquals('is_home', true);
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                foreach ($rows as $row) {
                    $pageIds[] = $row['id'];
                }

            } else if ($id = $select->getPart(Kwf_Component_Select::WHERE_ID)) {
                if ($this->_getPageData($id)) {
                    $accept = true;
                    if ($parentData) {
                        $accept = false;
                        $i = $id;
                        while ($pd = $this->_getPageData($i)) {
                            $i = $pd['parent_id'];
                            if ($i == $parentData->dbId) {
                                $accept = true;
                                break;
                            }
                        }
                    }
                    if ($accept) {
                        $pd = $this->_getPageData($id);
                        if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                            $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
                            $class = $this->_settings['component'][$pd['component']];
                            if (in_array($class, $selectClasses)) {
                                $pageIds[] = $id;
                            }
                        } else {
                            $pageIds[] = $id;
                        }
                    }
                }
            } else if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
                $keys = array();
                foreach ($selectClasses as $selectClass) {
                    $key = array_search($selectClass, $this->_settings['component']);
                    if ($key) $keys[] = $key;
                }

                $s = new Kwf_Model_Select();
                $s->whereEquals('component', array_unique($keys));
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                foreach ($rows as $row) {
                    $pageIds[] = $row['id'];
                }
                /*
                foreach ($keys as $key) {
                    if (isset($this->_pageComponent[$key])) {
                        $pageIds = array_merge($pageIds, $this->_pageComponent[$key]);
                    }
                }
                */
            } else {
                throw new Kwf_Exception("This would return all pages. You don't want this.");
            }

            if ($select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {

                $subroot = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
                $subroot = $subroot[0];

                if (!isset($this->_basesCache[$subroot->componentId])) {
                    //alle category komponenten der aktuellen domain suchen
                    $this->_basesCache[$subroot->componentId] = Kwf_Component_Data_Root::getInstance()->
                        getComponentsBySameClass($this->getClass(), array('subroot' => $subroot));
                }


                $allowedPageIds = array();
                foreach ($pageIds as $pageId) {
                    $allowed = false;
                    foreach ($this->_basesCache[$subroot->componentId] as $base) {
                        $id = $pageId;
                        while (!$allowed && ($pd = $this->_getPageData($id))) {
                            $id = $pd['parent_id'];
                            if ($id == $base->componentId) $allowed = true;
                        }
                        /*
                        auskommentiert, das ist langsam
                        und es muss mir erst wer zeigen *wo* das wirklich benötigt wird
                        if (!$allowed) {
                            $component = Kwf_Component_Data_Root::getInstance()
                                ->getComponentById($id)->parent;
                            while (!$allowed && $component) {
                                if ($component->componentId == $base->componentId) {
                                    $allowed = true;
                                }
                                $component = $component->parent;
                            }
                        }
                        */
                    }
                    if ($allowed) $allowedPageIds[] = $pageId;
                }

                $pageIds = $allowedPageIds;
            }

        }

        return $pageIds;
    }

    protected function _createData($parentData, $id, $select)
    {
        $page = $this->_getPageData($id);

        if (!$parentData || ($parentData->componentClass == $this->_class && $page['parent_id'])) {
            $c = array();
            if ($select->hasPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
                $c['ignoreVisible'] = $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
            }
            $parentData = Kwf_Component_Data_Root::getInstance()
                                ->getComponentById($page['parent_id'], $c);
            if (!$parentData) return null; // Kommt vor wenn data gefunden wird, parentData aber invisible ist
        }
        $pData = $parentData;
        while (is_numeric($pData->componentId)) $pData = $pData->parent;
        if ($pData->componentClass != $this->_class) return null;
        return parent::_createData($parentData, $id, $select);
    }

    protected function _getComponentIdFromRow($parentData, $id)
    {
        return $id;
    }

    protected function _formatConfig($parentData, $id)
    {
        $data = array();
        $page = $this->_getPageData($id);
        $data['filename'] = $page['filename'];
        $data['rel'] = '';
        $data['name'] = $page['name'];
        $data['isPage'] = true;
        $data['isPseudoPage'] = true;
        $data['componentId'] = $this->_getComponentIdFromRow($parentData, $id);
        $data['componentClass'] = $this->_getChildComponentClass($page['component'], $parentData);
        $data['row'] = (object)$page;
        $data['parent'] = $parentData;
        $data['isHome'] = $page['is_home'];
        if (!$page['visible']) {
            $data['invisible'] = true;
        }
        return $data;
    }
    protected function _getIdFromRow($id)
    {
        return $id;
    }

    protected function _getDataClass($config, $id)
    {
        $page = $this->_getPageData($id);
        if ($page['is_home']) {
            return 'Kwf_Component_Data_Home';
        } else {
            return parent::_getDataClass($config, $id);
        }
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        $ret['pseudoPage'] = true;
        $ret['page'] = true;
        $ret['table'] = true;
        $ret['pageGenerator'] = true;
        $ret['hasHome'] = true;
        return $ret;
    }


    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);

        $ret['actions']['delete'] = true;
        $ret['actions']['copy'] = true;
        $ret['actions']['visible'] = true;
        if ($this->getGeneratorFlag('hasHome')) {
            $ret['actions']['makeHome'] = true;
        }

        // Bei Pages muss nach oben gesucht werden, weil Klasse von Generator
        // mit Komponentklasse übereinstimmen muss
        $c = $component;
        while ($c && $c->componentClass != $this->getClass()) {
            $c = $c->parent;
        }
        if ($c) { //TODO warum tritt das auf?
            $ret['editControllerComponentId'] = $c->componentId;
        }

        $ret['icon'] = 'page';
        if ($component->isHome) {
            $ret['iconEffects'][] = 'home';
        } else if (!$component->visible) {
            $ret['iconEffects'][] = 'invisible';
        }
        $ret['allowDrag'] = true;
        //allowDrop wird in PagesController gesetzt da *darunter* eine page möglich ist

        return $ret;
    }

    public function getStaticCacheVarsForMenu()
    {
        $ret = array();
        $ret[] = array(
            'model' => $this->getModel()
        );
        return $ret;
    }


    public function getDuplicateProgressSteps($source)
    {
        $ret = 1;
        $ret += Kwc_Admin::getInstance($source->componentClass)->getDuplicateProgressSteps($source);
        foreach ($this->_getChildPageIds($source->id) as $i) {
            $data = $this->getChildData(null, array('id'=>$i, 'ignoreVisible'=>true));
            $data = array_shift($data);
            $ret += $this->getDuplicateProgressSteps($data);
        }
        return $ret;
    }

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }
        if (!Kwf_Component_Generator_Abstract::getInstances($parentTarget, array('whereGeneratorClass'=>get_class($this)))) {
            throw new Kwf_Exception("you must call this only with the correct target");
        }

        $sourceId = $source->id;
        $parentSourceId = $source->parent->componentId;
        $parentTargetId = $parentTarget->componentId;
        unset($source);
        unset($parentTarget);
        $targetId = $this->_duplicatePageRecursive($parentSourceId, $parentTargetId, $sourceId, $progressBar);
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentById($targetId, array('ignoreVisible'=>true));
    }

    private function _duplicatePageRecursive($parentSourceId, $parentTargetId, $childId, Zend_ProgressBar $progressBar = null)
    {
        $pd = $this->_getPageData($childId);
        if ($progressBar) $progressBar->next(1, trlKwf("Pasting {0}", $pd['name']));

        $data = array();
        $data['parent_id'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($parentTargetId, array('ignoreVisible'=>true))
            ->dbId;
        $sourceRow = $this->getModel()->getRow($childId);
        $newRow = $sourceRow->duplicate($data);

        //force reload to have the new row loaded
        $this->_pageDataLoaded = false; //TODO do this only once

                                                        //ids are numeric, we don't have to use parentSource/parentTarget
        $source = Kwf_Component_Data_Root::getInstance()->getComponentById($childId, array('ignoreVisible'=>true));
        $target = Kwf_Component_Data_Root::getInstance()->getComponentById($newRow->id, array('ignoreVisible'=>true));
        if (!$target) {
            throw new Kwf_Exception("didn't find just duplicated component '$newRow->id' below '{$parentTarget->componentId}'");
        }

        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target, $progressBar);

        $sourceId = $source->componentId;
        $targetId = $target->componentId;
        unset($source);
        unset($target);
        unset($sourceRow);
        unset($newRow);

        /*
        echo round(memory_get_usage()/1024/1024, 2)."MB";
        echo " gen: ".Kwf_Component_Generator_Abstract::$objectsCount.', ';
        echo " data: ".Kwf_Component_Data::$objectsCount.', ';
        echo " row: ".Kwf_Model_Row_Abstract::$objectsCount.'';
        $s = microtime(true);
        */
        Kwf_Component_Data_Root::getInstance()->freeMemory();
        /*
        echo ' / '.round((microtime(true)-$s)*1000, 2).' ms ';
        echo ' / '.round(memory_get_usage()/1024/1024, 2)."MB";
        echo " gen: ".Kwf_Component_Generator_Abstract::$objectsCount.', ';
        echo " data: ".Kwf_Component_Data::$objectsCount.', ';
        echo " row: ".Kwf_Model_Row_Abstract::$objectsCount.'';
        //p(Kwf_Component_ModelObserver::getInstance()->getProcess());
        //var_dump(Kwf_Model_Row_Abstract::$objectsByModel);
        //var_dump(Kwf_Component_Data::$objectsById);
        echo "\n";
        */

        foreach ($this->_getChildPageIds($childId) as $i) {
            if ($i != $targetId) { //no endless recursion id page is pasted below itself
                $this->_duplicatePageRecursive($sourceId, $targetId, $i, $progressBar);
            }
        }

        return $targetId;
    }

    public function getNameColumn()
    {
        return 'name';
    }

    public function getFilenameColumn()
    {
        return 'filename';
    }

    public function getPagePropertiesForm($componentOrParent)
    {
        return new Kwc_Root_Category_GeneratorForm($componentOrParent, $this);
    }

    public function getUseMobileBreakpoints()
    {
        return $this->_useMobileBreakpoints;
    }

    public function getDeviceVisible(Kwf_Component_Data $data)
    {
        if ($this->_useMobileBreakpoints) {
            return $data->row->device_visible;
        } else {
            return parent::getDeviceVisible($data);
        }
    }
}
