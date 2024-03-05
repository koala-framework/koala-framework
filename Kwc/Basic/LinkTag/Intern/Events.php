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

    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        self::$_pageIds[$this->_class] = null;
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $pageIds = $this->_getPageIdsFromRecursiveEvent($event);
        $this->_deleteCacheForTarget($pageIds, true, $event->component->getSubroot());
    }

    public function onRecursiveRemovedAdded(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $pageIds = $this->_getPageIdsFromRecursiveEvent($event);
        $this->_deleteCacheForTarget($pageIds, true, $event->component->getSubroot());
        $this->_changeHasContentForTarget($pageIds, true, $event->component->getSubroot());
    }

    public function onPageRemovedAdded(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        $this->_deleteCacheForTarget(array((string)$event->component->dbId), false, $event->component->getSubroot());
        $this->_changeHasContentForTarget(array((string)$event->component->dbId), false, $event->component->getSubroot());
    }

    //usually child componets can be deleted using %, but not those from pages table as the ids always start with numeric
    //this method returns all child ids needed for deleting recursively
    private function _getPageIdsFromRecursiveEvent(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $c = $event->component;
        $ids = array();
        if ($c->isPage) {
            $ids[] = (string)$c->dbId;
        }
        if (isset($c->generator) && $c->generator instanceof Kwc_Root_Category_Generator) {
            foreach ($c->generator->getRecursivePageChildIds($c->dbId) as $id) {
                $ids[] = (string)$id;
            }
        }
        return $ids;
    }

    protected function _deleteCacheForTarget($targetIds, $includeSubpages, $subroot)
    {
        if (!$targetIds) return;
        if (count($targetIds) > 1000) {
            $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class, $subroot));
            $this->fireEvent(new Kwf_Component_Event_Page_RecursiveUrlChanged($this->_class, $subroot));
            return;
        }
        $dbIds = self::_getComponentDbIdsForTarget($this->_class, $targetIds, $includeSubpages, $subroot);
        if (count($dbIds) > 1000) {
            $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class, $subroot));
            $this->fireEvent(new Kwf_Component_Event_Page_RecursiveUrlChanged($this->_class, $subroot));
            return;
        }
        foreach ($dbIds as $dbId) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
                if ($c->isPage) {
                    $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
                }
            }
        }
    }

    private function _changeHasContentForTarget($targetIds, $includeSubpages, $subroot)
    {
        foreach (self::getComponentsForTarget($this->_class, $targetIds, $includeSubpages, $subroot) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged($this->_class, $c));
        }
    }

    private static function _getComponentDbIdsForTarget($componentClass, $targetIds, $includeSubpages, $subroot)
    {
        if (!isset(self::$_pageIds[$componentClass])) {
            $dbId = $subroot->getDomainComponent() ? $subroot->getDomainComponent()->dbId : $subroot->dbId;
            $select = new Kwf_Model_Select();
            $select->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('parent_subroot_id', $dbId),
                new Kwf_Model_Select_Expr_Like('parent_subroot_id', $dbId . '-%')
            )));
            $possibleTargets = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel')->getIds($select);
            $ids = array();
            $model = Kwc_Abstract::createOwnModel($componentClass);
            $select = $model->select()->whereEquals('target', $possibleTargets);
            foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select) as $row) {
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
                foreach ($targetIds as $targetId) {
                    if ((string)$targetPageId == $targetId
                        || substr($targetPageId, 0, strlen($targetId)+1) == $targetId.'-'
                        || substr($targetPageId, 0, strlen($targetId)+1) == $targetId.'_'
                    ) {
                        $ret = array_merge($ret, $dbIds);
                        break;
                    }
                }
            } else {
                if (in_array((string)$targetPageId, $targetIds, true)) {
                    $ret = array_merge($ret, $dbIds);
                }
            }
        }
        return $ret;
    }

    //used in trl
    public static function getComponentsForTarget($componentClass, $targetIds, $includeSubpages, $subroot)
    {
        $ret = array();
        foreach (self::_getComponentDbIdsForTarget($componentClass, $targetIds, $includeSubpages, $subroot) as $dbId) {
            $ret = array_merge($ret, Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId));
        }
        return $ret;
    }

}
