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
        if (!array_key_exists($id, $this->_pageDataCache)) {

            $cacheId = 'pd-'.$id;
            $ret = Kwf_Cache_Simple::fetch($cacheId);
            if ($ret === false) {
                Kwf_Benchmark::count('GenPage::loadPageData');
                $cols = array('id', 'pos', 'is_home', 'name', 'filename', 'visible', 'component', 'hide', 'custom_filename', 'parent_id', 'parent_subroot_id');
                if ($this->_useMobileBreakpoints) $cols[] = 'device_visible';
                $ret = $this->_getModel()->fetchColumnsByPrimaryId($cols, $id);
                if ($ret) {
                    if ($ret['is_home']) $ret['visible'] = 1;
                    $ret['parent_visible'] = $ret['visible'];
                    $i = $ret['parent_id'];
                    $ret['parent_ids'] = array($i);
                    while (is_numeric($i)) {
                        $pd = $this->_getPageData($i);
                        if ($pd) {
                            $ret['parent_ids'][] = $pd['parent_id'];
                            if (count($ret['parent_ids']) > 20) {
                                throw new Kwf_Exception('probably endless recursion with parents');
                            }
                            $ret['parent_visible'] = $ret['parent_visible'] && $pd['visible'];
                            $i = $pd['parent_id'];
                        } else {
                            //page seems to be floating (without parent)
                            $ret = null;
                            break;
                        }
                    }
                } else {
                    $ret = null;
                }
                Kwf_Cache_Simple::add($cacheId, $ret);
            }
            $this->_pageDataCache[$id] = $ret;
        }
        return $this->_pageDataCache[$id];
    }

    private function _getChildPageIds($parentId)
    {
        $cacheId = 'pcIds-'.$parentId;
        $ret = Kwf_Cache_Simple::fetch($cacheId);
        if ($ret === false) {
            Kwf_Benchmark::count('GenPage::query',  'childIds('.$parentId.')');

            $select = new Kwf_Model_Select();
            if (is_numeric($parentId)) {
                $select->whereEquals('parent_id', $parentId);
            } else {
                $select->where(new Kwf_Model_Select_Expr_Like('parent_id', $parentId.'%'));
            }
            $select->order('pos');
            $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $select, array('columns'=>array('id')));
            $ret = array();
            foreach ($rows as $row) {
                $ret[] = $row['id'];
            }
            Kwf_Cache_Simple::add($cacheId, $ret);
        }
        return $ret;
    }

    /**
     * Returns all recursive children of a page
     */
    public function getRecursivePageChildIds($parentId)
    {
        $select = new Kwf_Model_Select();
        $ret = $this->_getChildPageIds($parentId);
        foreach ($ret as $i) {
            $ret = array_merge($ret, $this->getRecursivePageChildIds($i));
        }
        return $ret;
    }

    //called by GeneratorEvents when model changes
    public function pageDataChanged()
    {
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
            if (!$page) continue; //can happen for floating page (without valid parent)
            if ($select->hasPart(Kwf_Component_Select::WHERE_SHOW_IN_MENU)) {
                $menu = $select->getPart(Kwf_Component_Select::WHERE_SHOW_IN_MENU);
                if ($menu == $page['hide']) continue;
            }
            if ($select->getPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
            } else if (!Kwf_Component_Data_Root::getShowInvisible()) {
                if (!$page['parent_visible']) continue;
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
                $s->whereEquals('parent_subroot_id', $parentData->getSubroot()->dbId); //performance to look only in subroot - correct filterting done below
                Kwf_Benchmark::count('GenPage::query', 'home');
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                $homePages = array();
                foreach ($rows as $row) {
                    $homePages[] = $row['id'];
                }

                foreach ($homePages as $pageId) {
                    $pd = $this->_getPageData($pageId);
                    if (substr($pd['parent_id'], 0, strlen($parentId)) == $parentId) {
                        $pageIds[] = $pageId;
                        continue;
                    }
                    foreach ($pd['parent_ids'] as $pageParentId) {
                        if ($pageParentId == $parentId) {
                            $pageIds[] = $pageId;
                            break;
                        }
                    }
                }

            } else if ($select->hasPart(Kwf_Component_Select::WHERE_FILENAME)) {
                $filename = $select->getPart(Kwf_Component_Select::WHERE_FILENAME);
                $cacheId = 'pcFnIds-'.$parentId.'-'.$filename;
                $pageIds = Kwf_Cache_Simple::fetch($cacheId);
                if ($pageIds === false) {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('filename', $filename);
                    if (is_numeric($parentId)) {
                        $s->whereEquals('parent_id', $parentId);
                    } else {
                        $s->where(new Kwf_Model_Select_Expr_Like('parent_id', $parentId.'%'));
                    }
                    Kwf_Benchmark::count('GenPage::query', 'filename');
                    $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                    $pageIds = array();
                    foreach ($rows as $row) {
                        $pageIds[] = $row['id'];
                    }
                    if ($pageIds) {
                        Kwf_Cache_Simple::add($cacheId, $pageIds);
                    } else {
                        $s->order('date', 'DESC');
                        $rows = $this->getHistoryModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns' => array('page_id')));
                        foreach ($rows as $row) {
                            $pageIds[] = $row['page_id'];
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
                if (is_numeric($parentId)) {
                    $s->whereEquals('parent_id', $parentId);
                } else {
                    $s->where(new Kwf_Model_Select_Expr_Like('parent_id', $parentId.'%'));
                }
                $s->order('pos');
                Kwf_Benchmark::count('GenPage::query', 'component');
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('id')));
                foreach ($rows as $row) {
                    $pageIds[] = $row['id'];
                }

            } else {

                $pageIds = $this->_getChildPageIds($parentId);

            }

        } else {

            $pagesSelect = new Kwf_Model_Select();

            if ($id = $select->getPart(Kwf_Component_Select::WHERE_ID)) {
                //query only by id, no db query required
                $pageIds = array($id);

                if ($sr = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT)) {
                    $pd = $this->_getPageData($id);
                    if ($pd['parent_subroot_id'] != $sr[0]->dbId) {
                        $pageIds = array();
                    }
                }

                if ($pageIds && $select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                    $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
                    $keys = array();
                    foreach ($selectClasses as $selectClass) {
                        $key = array_search($selectClass, $this->_settings['component']);
                        if ($key && !in_array($key, $keys)) $keys[] = $key;
                    }
                    $pd = $this->_getPageData($id);
                    if (!in_array($pd['component'], $keys)) {
                        $pageIds = array();
                    }
                }

                if ($pageIds && $select->getPart(Kwf_Component_Select::WHERE_HOME)) {
                    $pd = $this->_getPageData($id);
                    if (!$pd['is_home']) {
                        $pageIds = array();
                    }
                }

            } else {
                $benchmarkType = '';
                if ($select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {

                    $subroot = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
                    $subroot = $subroot[0];
                    $pagesSelect->whereEquals('parent_subroot_id', $subroot->dbId);
                    $benchmarkType .= 'subroot ';
                }

                if ($select->getPart(Kwf_Component_Select::WHERE_HOME)) {
                    $pagesSelect->whereEquals('is_home', true);
                    $benchmarkType .= 'home ';
                }
                if ($id = $select->getPart(Kwf_Component_Select::WHERE_ID)) {
                    $pagesSelect->whereEquals('id', $id);
                    $benchmarkType .= 'id ';
                }
                if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                    $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
                    $keys = array();
                    foreach ($selectClasses as $selectClass) {
                        $key = array_search($selectClass, $this->_settings['component']);
                        if ($key && !in_array($key, $keys)) $keys[] = $key;
                    }
                    $pagesSelect->whereEquals('component', $keys);
                    $benchmarkType .= 'component ';
                }
                Kwf_Benchmark::count('GenPage::query', "noparent(".trim($benchmarkType).")");
                $rows = $this->_getModel()->export(Kwf_Model_Interface::FORMAT_ARRAY, $pagesSelect, array('columns'=>array('id')));
                $pageIds = array();
                foreach ($rows as $row) {
                    $pageIds[] = $row['id'];
                }
            }

            if ($parentData) {
                $parentId = $parentData->dbId;
                foreach ($pageIds as $k=>$pageId) {
                    $match = false;
                    $pd = $this->_getPageData($pageId);
                    if (!$pd) continue;
                    if (substr($pd['parent_id'], 0, strlen($parentId)) == $parentId) {
                        $match = true;
                    }
                    if (!$match) {
                        foreach ($pd['parent_ids'] as $pageParentId) {
                            if ($pageParentId == $parentId) {
                                $match = true;
                                break;
                            }
                        }
                    }
                    if (!$match) {
                        unset($pageIds[$k]);
                    }
                }
            }
        }

        return $pageIds;
    }

    protected function _createData($parentData, $id, $select)
    {
        $page = $this->_getPageData($id);

        if (!$parentData || ($parentData->componentClass == $this->_class && $page['parent_id'])) {
            $parentData = $page['parent_id'];
        }

        foreach ($page['parent_ids'] as $i) {
            if (!is_numeric($i)) {
                $c = array();
                if ($select->hasPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
                    $c['ignoreVisible'] = $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
                }
                $pData = Kwf_Component_Data_Root::getInstance()
                                    ->getComponentById($i, $c);
                if (!$pData || $pData->componentClass != $this->_class) {
                    return null;
                }
            }
        }

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
        if (!is_object($parentData)) {
            $data['_lazyParent'] = $parentData;
        } else {
            $data['parent'] = $parentData;
        }
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
        $ret['showInLinkInternAdmin'] = true;
        $ret['pseudoPage'] = true;
        $ret['page'] = true;
        $ret['table'] = true;
        $ret['pageGenerator'] = true;
        if (!isset($this->_settings['hasHome']) || $this->_settings['hasHome']) {
            $ret['hasHome'] = true;
        }
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
        if ($sourceRow->is_home) {
            //copy is_home only if target has no home yet
            $t = Kwf_Component_Data_Root::getInstance()->getComponentById($parentTargetId, array('ignoreVisible'=>true));
            while ($t && !Kwc_Abstract::getFlag($t->componentClass, 'hasHome')) {
                $t = $t->parent;
            }
            if (!$t || $t->getChildPage(array('home' => true, 'ignoreVisible'=>true), array())) {
                $data['is_home'] = false;
            }
        }
        $newRow = $sourceRow->duplicate($data);

        //clear cache in here as while duplicating the modelobserver might be disabled
        Kwf_Cache_Simple::delete('pcIds-'.$newRow->parent_id);

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

    public function getHistoryModel()
    {
        return Kwf_Model_Abstract::getInstance($this->_settings['historyModel']);
    }
}
