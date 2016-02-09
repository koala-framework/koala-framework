<?php
class Kwf_Component_Events_ViewCache extends Kwf_Events_Subscriber
{
    private $_updates = array();
    private $_pageParentChanges = array();

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Kwf_Events_Event_Row_UpdatesFinished',
            'callback' => 'onRowUpdatesFinished'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveContentChanged',
            'callback' => 'onRecursiveContentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_MasterContentChanged',
            'callback' => 'onMasterContentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveMasterContentChanged',
            'callback' => 'onRecursiveMasterContentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClass_MasterContentChanged',
            'callback' => 'onClassMasterContentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onPageRecursiveUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onComponentRecursiveRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
            'callback' => 'onComponentClassContentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClass_AllPartialChanged',
            'callback' => 'onComponentClassAllPartialChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClass_PartialChanged',
            'callback' => 'onComponentClassPartialChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClass_PartialsChanged',
            'callback' => 'onComponentClassPartialsChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClassPage_ContentChanged',
            'callback' => 'onComponentClassPageContentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageParentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Events_Event_Media_Changed',
            'callback' => 'onMediaChanged'
        );
        return $ret;
    }

    public function onRowUpdatesFinished(Kwf_Events_Event_Row_UpdatesFinished $event)
    {
        if ($this->_updates) {
            $or = array();
            foreach ($this->_updates as $key => $values) {
                if ($key === 'component_id') {
                    $or[] = new Kwf_Model_Select_Expr_And(array(
                        new Kwf_Model_Select_Expr_Equal('component_id', array_unique($values)),
                        new Kwf_Model_Select_Expr_Equal('type', 'component'),
                    ));
                } else if ($key === 'master-component_id') {
                    $or[] = new Kwf_Model_Select_Expr_And(array(
                        new Kwf_Model_Select_Expr_Equal('component_id', array_unique($values)),
                        new Kwf_Model_Select_Expr_Equal('type', 'master'),
                    ));
                } else {
                    $and = array();
                    foreach ($values as $k => $v) {
                        if (substr($v, -1) == '%') {
                            $v = substr($v, 0, -1);
                            $and[] = new Kwf_Model_Select_Expr_Or(array(
                                new Kwf_Model_Select_Expr_Equal($k, $v),
                                new Kwf_Model_Select_Expr_Like($k, $v.'-%'),
                                new Kwf_Model_Select_Expr_Like($k, $v.'_%'),
                            ));
                        } else if (strpos($v, '%') !== false) {
                            $and[] = new Kwf_Model_Select_Expr_Like($k, $v);
                        } else {
                            $and[] = new Kwf_Model_Select_Expr_Equal($k, $v);
                        }
                    }
                    $and = new Kwf_Model_Select_Expr_And($and);
                    if (!in_array($and, $or)) {
                        $or[] = $and;
                    }
                }
            }
            $select = new Kwf_Model_Select();
            $select->where($or[0]);
            unset($or[0]);
            foreach ($or as $i) {
                $s = new Kwf_Model_Select();
                $s->where($i);
                $select->union($s);
            }
            Kwf_Component_Cache::getInstance()->deleteViewCache($select);
            $this->_updates = array();
        }

        foreach ($this->_pageParentChanges as $changes) {
            $oldParentId = $changes['oldParentId'];
            $newParentId = $changes['newParentId'];
            $componentId = $changes['componentId'];
            $length = strlen($oldParentId);
            $like = $oldParentId . '_' . $componentId;
            $model = Kwf_Component_Cache::getInstance()->getModel();
            while ($model instanceof Kwf_Model_Proxy) $model = $model->getProxyModel();
            if ($model instanceof Kwf_Model_Db) {
                $db = Kwf_Registry::get('db');
                $newParentId = $db->quote($newParentId);
                $where[] = 'expanded_component_id = ' . $db->quote($like);
                $where[] = 'expanded_component_id LIKE ' . str_replace('_', '\_', $db->quote($like . '-%'));
                $where[] = 'expanded_component_id LIKE ' . str_replace('_', '\_', $db->quote($like . '_%'));
                $sql = "UPDATE cache_component
                    SET expanded_component_id=CONCAT(
                        $newParentId, SUBSTRING(expanded_component_id, $length)
                    )
                    WHERE " . implode(' OR ', $where);
                $model->executeSql($sql);
                $this->_log("expanded_component_id={$like}%->{$newParentId}");
            } else {
                $model = Kwf_Component_Cache::getInstance()->getModel();
                $select = $model->select()->where(
                    new Kwf_Model_Select_Expr_Like('expanded_component_id', $like . '%')
                );
                foreach ($model->getRows($select) as $row) {
                    $oldExpandedId = $row->expanded_component_id;
                    $newExpandedId = $newParentId . substr($oldExpandedId, $length);
                    $row->expanded_component_id = $newExpandedId;
                    $row->save();
                    $this->_log("expanded_component_id={$oldExpandedId}->{$newExpandedId}");
                }
            }
        }
        $this->_pageParentChanges = array();
    }

    public function onContentChange(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $this->_updates['component_id'][] = $event->component->componentId;
        $this->_log("component_id={$event->component->componentId} type=component");
    }

    public function onRecursiveContentChange(Kwf_Component_Event_Component_RecursiveContentChanged $event)
    {
        foreach ($this->_getParentComponentsForRecursive($event) as $c) {
            $this->_updates[] = array(
                'type' => 'component',
                'expanded_component_id' => $c->getExpandedComponentId() . '%',
                'component_class' => $event->class
            );
            $this->_log("type=component expanded_component_id={$c->getExpandedComponentId()}% component_class=$event->class");
        }
    }

    public function onMasterContentChange(Kwf_Component_Event_Component_MasterContentChanged $event)
    {
        $this->_updates['master-component_id'][] = $event->component->componentId;
        $this->_log("component_id={$event->component->componentId} type=master");
    }

    public function onRecursiveMasterContentChange(Kwf_Component_Event_Component_RecursiveMasterContentChanged $event)
    {
        foreach ($this->_getParentComponentsForRecursive($event) as $component) {
            $this->_updates[] = array(
                'type' => 'master',
                'expanded_component_id' => $component->getExpandedComponentId() . '%',
            );
            $this->_log("type=master expanded_component_id={$component->getExpandedComponentId()}%");
        }
    }

    public function onClassMasterContentChange(Kwf_Component_Event_ComponentClass_MasterContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'master',
        );
        $this->_log("type=master");
    }

    // namechanged and filnamechanged-events
    public function onPageChanged(Kwf_Component_Event_Component_Abstract $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'component_id' => $event->component->componentId
        );
        $this->_log("type=componentLink component_id={$event->component->componentId}");
    }

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        foreach ($this->_getParentComponentsForRecursive($event) as $component) {
            $this->_updates[] = array(
                'type' => 'componentLink',
                'expanded_component_id' => $component->getExpandedComponentId() . '%',
            );
            $this->_log("type=componentLink expanded_component_id={$component->getExpandedComponentId()}%");
        }
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        //the component itself
        $this->_updates[] = array(
            //remove all types
            'component_id' => (string)$event->component->componentId
        );
        $this->_log("component_id={$event->component->componentId}");

        //all child components
        $changedComponent = $event->component;

        foreach ($this->_getParentComponentsForRecursive($event) as $component) {
            $pattern = $component->getExpandedComponentId() . '%';
            $changedChildIdPostfix = substr($changedComponent->componentId, strlen($component->componentId));
            if ($changedChildIdPostfix) {
                $pattern .= $changedChildIdPostfix . '%';
            }
            $this->_updates[] = array(
                'expanded_component_id' => $pattern,
            );
            $this->_log("expanded_component_id=$pattern");
        }
    }

    public function onComponentClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $subroot = null;
        if ($event->subroot) $subroot = $event->subroot->getSubroot();
        if ($subroot) {
            $id = $subroot->componentId . '%';
            $this->_updates[] = array(
                'type' => 'component',
                'component_class' => $event->class,
                'expanded_component_id' => $id
            );
            $this->_log("type=component expanded_component_id=$id component_class=$event->class");
        } else {
            $this->_updates[] = array(
                'type' => 'component',
                'component_class' => $event->class
            );
            $this->_log("type=component component_class=$event->class");
        }
    }

    public function onComponentClassAllPartialChanged(Kwf_Component_Event_ComponentClass_AllPartialChanged $event)
    {
        $subroot = null;
        if ($event->subroot) $subroot = $event->subroot->getSubroot();
        if ($subroot) {
            $id = $subroot->componentId . '%';
            $this->_updates[] = array(
                'type' => 'partial',
                'component_class' => $event->class,
                'expanded_component_id' => $id
            );
            $this->_log("type=partial expanded_component_id=$id component_class=$event->class");
        } else {
            $this->_updates[] = array(
                'type' => 'partial',
                'component_class' => $event->class
            );
            $this->_log("type=partial component_class=$event->class");
        }
    }

    public function onComponentClassPartialChanged(Kwf_Component_Event_ComponentClass_PartialChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'partial',
            'component_class' => $event->class,
            'value' => $event->id
        );
        $this->_log("type=partial component_class=$event->class value=$event->id");
    }

    public function onComponentClassPartialsChanged(Kwf_Component_Event_ComponentClass_PartialsChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'partials',
            'component_class' => $event->class
        );
        $this->_log("type=partials component_class=$event->class");
    }

    public function onComponentClassPageContentChanged(Kwf_Component_Event_ComponentClassPage_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'page_db_id' => $event->page->dbId,
            'component_class' => $event->class
        );
        $this->_log("type=component page_db_id={$event->page->dbId} component_class=$event->class");
    }

    public function onPageParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $oldParentId = $event->oldParent->getExpandedComponentId();
        $newParentId = $event->newParent->getExpandedComponentId();
        $this->_pageParentChanges[] = array(
            'oldParentId' => $oldParentId,
            'newParentId' => $newParentId,
            'componentId' => $event->component->componentId
        );

        $oldPlugins = array();
        $c = $event->oldParent;
        while ($c) {
            $oldPlugins = array_merge($oldPlugins, Kwc_Abstract::getSetting($c->componentClass, 'pluginsInherit'));
            $c = $c->parent;
        }

        $newPlugins = array();
        $c = $event->newParent;
        while ($c) {
            $oldPlugins = array_merge($oldPlugins, Kwc_Abstract::getSetting($c->componentClass, 'pluginsInherit'));
            $c = $c->parent;
        }

        $oldPlugins = array_unique($oldPlugins);
        $newPlugins = array_unique($newPlugins);
        sort($oldPlugins);
        sort($newPlugins);

        if ($oldPlugins != $newPlugins) {
            //delete all components as plugins are in view cache
            $this->_updates[] = array(
                'type' => 'component',
                'expanded_component_id' => $event->component->getExpandedComponentId() . '%'
            );
            $this->_log("type=component expanded_component_id={$event->component->getExpandedComponentId()}%");
        }
    }

    private function _getParentComponentsForRecursive(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $c = $event->component;
        $ret = array();

        //get first parent that inherits (can be root)
        $i = $c;
        while ($i) {
            if ($i->inherits || $i->componentId == 'root') {
                $ret[] = $i;
                break;
            }
            $i = $i->parent;
        }

        //get parent pages from created by pageGenerators
        foreach (Kwf_Component_Data_Root::getInstance()->getPageGenerators() as $gen) {
            while ($c && !$c->inherits && !$c instanceof Kwf_Component_Data_Root && $c->componentClass !== $gen->getClass()) {
                $c = $c->parent;
            }
            if ($c && !in_array($c, $ret, true)) $ret[] = $c;
        }
        return $ret;
    }

    private function _log($msg)
    {
        $log = Kwf_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear $msg", Zend_Log::INFO);
        }
    }

    public function onMediaChanged(Kwf_Events_Event_Media_Changed $event)
    {
        Kwf_Media::clearCache($event->class, $event->component->componentId, $event->type);
        $log = Kwf_Events_Log::getInstance();
        if ($log) {
            $log->log("media cache clear class=$event->class id={$event->component->componentId} type=$event->type", Zend_Log::INFO);
        }
    }
}
