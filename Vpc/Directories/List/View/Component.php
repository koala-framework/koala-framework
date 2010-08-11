<?php
class Vpc_Directories_List_View_Component extends Vpc_Abstract_Composite_Component
    implements Vpc_Paging_ParentInterface, Vps_Component_Partial_Interface
{
    protected $_items;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = 'Vpc_Paging_Component';
        $ret['placeholder']['noEntriesFound'] = trlVps('No entries were found.');
        $ret['groupById'] = true;
        $ret['cssClass'] = 'webStandard';
        $ret['searchQueryFields'] = '*';
        $ret['partialClass'] = 'Vps_Component_Partial_Id'; // Eigentlich nur nötig wenn es Suche gibt, ansonsten reicht Vps_Component_Partial_Paging
        return $ret;
    }

    public function processInput(array $postData)
    {
        // Wenn es eine Search-Form gibt und diese nicht unter der eigenen Page
        // liegt, manuell processen - das Flag muss allerdings manuell gesetzt
        // werden!
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
        if ($searchForm && $searchForm->getComponent()->isSaved()) {
            $values = $searchForm->getComponent()->getFormRow()->toArray();
            unset($values['id']);
            $ret->searchLike($values, $this->_getSetting('searchQueryFields'));
        }

        // Limit-Setting beschränkt Einträge auf bestimmte Anzahl (zB für LiveSearch)
        if ($this->_hasSetting('limit')) {
            $ret->limit($this->_getSetting('limit'));
        }
        return $ret;
    }

    protected function _getPagingComponent()
    {
        return $this->getData()->getChildComponent('-paging');
    }

    public function getItemIds($count = null, $offset = null)
    {
        $select = $this->_getSelect();
        if (!$select) return array();
        if ($count) $select->limit($count, $offset);
        $itemDirectory = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($itemDirectory)) {
            $c = Vpc_Abstract::getComponentClassByParentClass($itemDirectory);
            $generator = Vps_Component_Generator_Abstract::getInstance($c, 'detail');
            $items = $generator->getChildIds(null, array('select'=>$select));
        } else {
            $items = $itemDirectory->getChildIds($select);
        }
        return $items;
    }

    protected function _getItems($select = null)
    {
        if (!$select) $select = $this->_getSelect();
        if (!$select) return array();
        $itemDirectory = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($itemDirectory)) {
            $c = Vpc_Abstract::getComponentClassByParentClass($itemDirectory);
            $generator = Vps_Component_Generator_Abstract::getInstance($c, 'detail');
            $items = $generator->getChildData(null, array('select'=>$select));
        } else {
            $items = $itemDirectory->getChildComponents($select);
        }
        foreach ($items as &$item) {
            $item->parent->getComponent()->callModifyItemData($item);
        }
        return $items;
    }

    public function getItems()
    {
        return $this->_getItems();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['directory'] = $this->getData()->parent->getComponent()->getItemDirectory();
        $ret['formSaved'] = null;
        if ($this->_getSearchForm()) {
            $ret['formSaved'] = $this->_getSearchForm()->getComponent()->isSaved();
        }

        return $ret;
    }

    // für helper partialPaging
    public function getPartialParams()
    {
        $paging = $this->_getPagingComponent();
        $ret = array();
        $ret['componentId'] = $this->getData()->componentId;
        $ret['count'] = $this->getPagingCount();
        if ($paging) {
            $ret = array_merge($ret, $paging->getComponent()->getPartialParams());
        }
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = $info;
        if ($partial instanceof Vps_Component_Partial_Random) {
            $select = $this->_getSelect()->limit(1, $nr);
            $ret['item'] = array_shift($this->_getItems($select));
        } else if ($partial instanceof Vps_Component_Partial_Paging) {
            if ($partial instanceof Vps_Component_Partial_Id) {
                $select = $this->_getSelect()->whereId($nr);
            } else if ($partial instanceof Vps_Component_Partial_Paging) {
                $select = $this->_getSelect()->limit(1, $nr);
            }
            $ret['item'] = array_shift($this->_getItems($select));
        } else {
            throw new Vps_Exception('Unsupported partial type '.get_class($partial));
        }
        return $ret;
    }

    public function getPagingCount($select = null)
    {
        if (!$select) $select = $this->_getSelect();
        if (!$select) return 0;

        $dir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($dir)) {
            $c = Vpc_Abstract::getComponentClassByParentClass($dir);
            $generator = Vps_Component_Generator_Abstract::getInstance($c, 'detail');
            $ret = $generator->countChildData(null, array('select'=>$select));
        } else {
            $ret = $dir->countChildComponents($select);
        }

        if ($select->hasPart(Vps_Model_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
            if ($ret > $limitCount) $ret = $limitCount;
        }
        return $ret;
    }

    // Liefert zurück, ob es noch nicht ausgegebene Elemente gibt, falls
    // das Setting Limit gesetzt wurde
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

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $ret = array_merge($ret, $this->_getCacheData());
        return $ret;
    }

    private function _getCacheData($nr = null)
    {
        $dir = $this->getData()->parent->getComponent()->getItemDirectory();
        $generator = null;
        if ($dir instanceof Vps_Component_Data) {
            $generator = $dir->getGenerator('detail');
        } else if (is_string($dir)) {
            $generator = Vps_Component_Generator_Abstract::getInstance(
                Vpc_Abstract::getComponentClassByParentClass($dir), 'detail'
            );
        }

        if ($generator) {
            $ret = $generator->getCacheVars($dir instanceof Vps_Component_Data ? $dir : null);
            if ($nr) {
                foreach ($ret as $k=>$i) {
                    if (!$i['field']) {
                        $ret[$k]['id'] = $nr;
                    }
                }
            }
            return $ret;
        }
        return array();
    }

    public function getPartialCacheVars($nr)
    {
        $ret = array();
        if (is_instance_of($this->getPartialClass(), 'Vps_Component_Partial_Id')) {
            $ret = array_merge($ret, $this->_getCacheData($nr));
        } else if (is_instance_of($this->getPartialClass(), 'Vps_Component_Partial_Paging')) {
            $ret = array_merge($ret, $this->_getCacheData());
        } else if (is_instance_of($this->getPartialClass(), 'Vps_Component_Partial_Random')) {
            $ret = array_merge($ret, $this->_getCacheData());
        }
        return $ret;
    }
}
