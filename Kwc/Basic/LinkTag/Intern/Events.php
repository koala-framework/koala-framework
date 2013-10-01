<?php
class Kwc_Basic_LinkTag_Intern_Events extends Kwc_Abstract_Events
{
    private static $_pageIds;

    public function getListeners()
    {
        $ret = parent::getListeners();
        if (Kwc_Abstract::createOwnModel($this->_class) instanceof Kwc_Basic_LinkTag_Intern_Model) {
            $ret[] = array(
                'class' => null,
                'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
                'callback' => 'onRecursiveUrlChanged'
            );
            $ret[] = array(
                'class' => null,
                'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
                'callback' => 'onRecursiveRemovedAdded'
            );
            $ret[] = array(
                'class' => null,
                'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
                'callback' => 'onRecursiveRemovedAdded'
            );
            $ret[] = array(
                'class' => null,
                'event' => 'Kwf_Component_Event_Page_Added',
                'callback' => 'onPageRemovedAdded'
            );
            $ret[] = array(
                'class' => null,
                'event' => 'Kwf_Component_Event_Page_Removed',
                'callback' => 'onPageRemovedAdded'
            );
        }
        return $ret;
    }

    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        self::$_pageIds[$this->_class] = null;
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        foreach ($this->_getPageIdsFromRecursiveEvent($event) as $pageId) {
            $this->_deleteCacheForTarget($pageId, true);
        }
    }

    public function onRecursiveRemovedAdded(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        foreach ($this->_getPageIdsFromRecursiveEvent($event) as $pageId) {
            $this->_deleteCacheForTarget($pageId, true);
        }
    }

    public function onPageRemovedAdded(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        $this->_deleteCacheForTarget($event->component->dbId, false);
    }

    //usually child componets can be deleted using %, but not those from pages table as the ids always start with numeric
    //this method returns all child ids needed for deleting recursively
    private function _getPageIdsFromRecursiveEvent(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $c = $event->component;
        $ids = array();
        if ($c->isPage) {
            $ids[] = $c->dbId;
        }
        if ($c->generator instanceof Kwc_Root_Category_Generator) {
            $ids = array_merge($ids, $c->generator->getRecursivePageChildIds($c->dbId));
        }
        return $ids;
    }

    private function _deleteCacheForTarget($targetId, $includeSubpages)
    {
        foreach (self::getComponentsForTarget($this->_class, $targetId, $includeSubpages) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
            if ($c->isPage) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
            }
        }
    }

    //used in trl
    public static function getComponentsForTarget($componentClass, $targetId, $includeSubpages)
    {
        if (!isset(self::$_pageIds[$componentClass])) {
            $ids = array();
            $model = Kwc_Abstract::createOwnModel($componentClass);
            foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY) as $row) {
                $target = $row['target'];
                if (!isset($ids[$target])) $ids[$target] = array();
                $ids[$target][] = $row['component_id'];
            }
            self::$_pageIds[$componentClass] = $ids;
        }
        $ret = array();
        foreach (self::$_pageIds[$componentClass] as $targetPageId => $dbIds) {
            $ids = array();
            if ($includeSubpages) {
                if ((string)$targetPageId == (string)$targetId
                    || substr($targetPageId, 0, strlen($targetId)+1) == $targetId.'-'
                    || substr($targetPageId, 0, strlen($targetId)+1) == $targetId.'_'
                ) {
                    $ids = $dbIds;
                }
            } else {
                if ((string)$targetPageId === (string)$targetId) {
                    $ids = $dbIds;
                }
            }
            foreach ($ids as $dbId) {
                $ret = array_merge($ret, Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId));
            }
        }
        return $ret;
    }

}
