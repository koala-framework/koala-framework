<?php
class Kwc_Directories_List_View_Component extends Kwc_Abstract_Composite_Component
    implements Kwc_Paging_ParentInterface, Kwf_Component_Partial_Interface
{
    protected $_items;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['paging'] = 'Kwc_Paging_Component';
        //$ret['generators']['child']['component']['count'] = 'Kwc_Directories_List_View_Count_Component';
        $ret['placeholder']['noEntriesFound'] = trlKwfStatic('No entries found');
        $ret['groupById'] = true;
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['searchQueryFields'] = '*';
        return $ret;
    }

    public final static function getPartialClass($componentClass)
    {
        if (Kwc_Abstract::hasSetting($componentClass, 'partialClass')) {
            return Kwc_Abstract::getSetting($componentClass, 'partialClass');
        }
        $generators = Kwc_Abstract::getSetting($componentClass, 'generators');
        if (isset($generators['child']['component']['searchForm'])) {
            return 'Kwf_Component_Partial_Id';
        } else if (isset($generators['child']['component']['paging']) && $generators['child']['component']['paging']) {
            return 'Kwf_Component_Partial_Paging';
        } else {
            return 'Kwf_Component_Partial_Stateless';
        }
    }

    public function processInput(array $postData)
    {
        // if search-form exists and it doesn't exists as under this page,
        // process it manually - the flag must be set manually!
        $searchForm = $this->_getSearchForm();
        if ($searchForm && !$searchForm->getComponent()->isProcessed()) {
            $searchForm->getComponent()->processInput($postData);
        }
    }

    protected function _getSearchForm()
    {
        $generators = $this->_getSetting('generators');
        if (isset($generators['child']['component']['searchForm'])) {
            return $this->getData()->getChildComponent('-searchForm');
        }
        return null;
    }

    public function hasSearchForm()
    {
        return !is_null($this->_getSearchForm());
    }

    public function getSearchForm()
    {
        return $this->_getSearchForm();
    }

    protected function _getSelect()
    {
        $ret = $this->getData()->parent->getComponent()->getSelect();
        if (!$ret) return $ret;

        $searchForm = $this->_getSearchForm();
        if ($searchForm && $this->getPartialClass($this->getData()->componentClass) != 'Kwf_Component_Partial_Id') {
            throw new Kwf_Exception(get_class($this) . ': if search-form ist used, you also have to use PartialId (use Setting "partialClass")');
        }

        if ($searchForm && $searchForm->getComponent()->isSaved()) {
            $ret = $this->_getSearchSelect($ret, $searchForm->getComponent()->getFormRow());
        }

        // setting to limit the count of entries (for example LiveSearch)
        if ($this->_hasSetting('limit')) {
            $ret->limit($this->_getSetting('limit'));
        }
        return $ret;
    }

    // rewrite this function if you want a specific search form select
    protected function _getSearchSelect($ret, $searchRow)
    {
        $values = $searchRow->toArray();
        unset($values['id']);
        $ret->where(new Kwf_Model_Select_Expr_SearchLike($values, $this->_getSetting('searchQueryFields')));
        return $ret;
    }

    protected function _getPagingComponent()
    {
        return $this->getData()->getChildComponent('-paging');
    }

    //override in List if needed
    public final function getItemIds($count = null, $offset = null)
    {
        $select = $this->_getSelect();
        if (!$select) return array();

        if ($count) {
            if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
                $ex = new Kwf_Exception("Can't use getItemIds with limit in select when \$count paramter is given. You probably should disable paging for {$this->getData()->componentClass}.");
                $ex->logOrThrow();
            }
            $select->limit($count, $offset);
        }

        return $this->getData()->parent->getComponent()->getItemIds($select);
    }

    //override in List if needed
    protected final function _getItems($select = null)
    {
        if (!$select) $select = $this->_getSelect();
        if (!$select) return array();

        return $this->getData()->parent->getComponent()->getItems($select);
    }

    //override in List if needed
    public final function getItems()
    {
        return $this->_getItems();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['directory'] = $this->getData()->parent->getComponent()->getItemDirectory();
        $ret['formSaved'] = null;
        if ($this->_getSearchForm()) {
            $ret['formSaved'] = $this->_getSearchForm()->getComponent()->isSaved();
        }

        return $ret;
    }

    // for helper partialPaging
    public function getPartialParams()
    {
        $select = $this->_getSelect();
        $ret = array();
        $ret['componentId'] = $this->getData()->componentId;
        $ret['count'] = $this->getPagingCount($select);
        $ret['disableCache'] = false;
        $ret['cacheLifetime'] = $this->getViewCacheLifetime();
        $ret['disableCacheParams'] = array();
        $paging = $this->_getPagingComponent();
        if ($paging) {
            $ret['paging'] = $paging->getComponent()->getPartialParams();
            $ret['disableCacheParams'][] = $ret['paging']['paramName'];
        }
        $search = $this->_getSearchForm();
        if ($search) {
            $ret['disableCacheParams'][] = $search->componentId.'-post';
        }
        $ret['noEntriesFound'] = $this->_getPlaceholder('noEntriesFound');
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = $info;
        if ($partial instanceof Kwf_Component_Partial_Random) {
            $select = $this->_getSelect()->limit(1, $nr);
            $items = $this->_getItems($select); // array_shift needs variable-reference
            $ret['item'] = array_shift($items);
        } else if ($partial instanceof Kwf_Component_Partial_Paging) {
            if ($partial instanceof Kwf_Component_Partial_Id) {
                $select = new Kwf_Component_Select();
                $select->whereId($nr);
            } else if ($partial instanceof Kwf_Component_Partial_Paging) {
                $select = $this->_getSelect()->limit(1, $nr);
            }
            $items = $this->_getItems($select);
            $ret['item'] = array_shift($items);
        } else {
            throw new Kwf_Exception('Unsupported partial type '.get_class($partial));
        }
        $ret['placeholder'] = $this->_getPlaceholder();
        $ret['bemClass'] = $this->_getBemClass('');
        return $ret;
    }

    public function getPagingCount($select = null)
    {
        if (!$select) $select = $this->_getSelect();
        if (!$select) return 0;

        $dir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($dir)) {
            $c = Kwc_Abstract::getComponentClassByParentClass($dir);
            $generator = Kwf_Component_Generator_Abstract::getInstance($c, 'detail');
            $ret = $generator->countChildData(null, $select);
        } else {
            $ret = $dir->countChildComponents($select);
        }

        if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Kwf_Model_Select::LIMIT_COUNT);
            if ($ret > $limitCount) $ret = $limitCount;
        }
        return $ret;
    }

    // if setting limit is set, it will return hidden elements with paging
    public function moreItemsAvailable()
    {
        $select = $this->_getSelect();
        if ($this->_hasSetting('limit')) {
            $select->unsetPart('limitCount');
        }
        return $this->getPagingCount($select) > $this->_getSetting('limit');
    }

    public function hasContent()
    {
        if ($this->getPagingCount() > 0) return true;
        return parent::hasContent();
    }

    public function getViewCacheLifetime()
    {
        $dir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (!$dir) {
            return parent::getViewCacheLifetime();
        } else if (is_string($dir)) {
            return call_user_func(array($dir, 'getViewCacheLifetimeForView'));
        } else {
            return $dir->getComponent()->getViewCacheLifetimeForView();
        }
    }

    public function getViewCacheSettings()
    {
        $ret = parent::getViewCacheSettings();
        $dir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (!$dir) {
            return null;
        } else if (is_string($dir)) {
            $ret['cacheTag'] = $dir;
        } else {
            $ret['cacheTag'] = $dir->componentId;
        }
        return $ret;
    }
}
