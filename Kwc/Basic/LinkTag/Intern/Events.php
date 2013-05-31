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

    //usually child componets can be deleted using %, but not those from pages table as the ids always start with numeric
    //this method returns all child ids needed for deleting recursively
    private function _getIdsFromRecursiveEvent(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $c = $event->component;
        $ids = array($c->dbId);
        $c = $c->getPageOrRoot();
        foreach (Kwf_Component_Data_Root::getInstance()->getPageGenerators() as $gen) {
            $ids = array_merge($ids, $gen->getRecursiveVisiblePageChildIds($c->dbId));
        }
        return $ids;
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        foreach ($this->_getIdsFromRecursiveEvent($event) as $childPageId) {
            foreach ($this->_getComponentsForTarget($childPageId, true) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
                if ($c->isPage) {
                    $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
                }
            }
        }
    }

    public function onRecursiveRemovedAdded(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        foreach ($this->_getIdsFromRecursiveEvent($event) as $childPageId) {
            foreach ($this->_getComponentsForTarget($childPageId, true) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
                if ($c->isPage) {
                    $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
                }
            }
        }
    }

    public function onPageRemovedAdded(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        foreach ($this->_getComponentsForTarget($event->component->dbId, false) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
            if ($c->isPage) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
            }
        }
    }

    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        self::$_pageIds[$this->_class] = null;
    }

    private function _getComponentsForTarget($targetId, $includeSubpages)
    {
        return self::getComponentsForTarget($this->_class, $targetId, $includeSubpages);
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
