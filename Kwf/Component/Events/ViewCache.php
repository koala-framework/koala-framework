<?php
class Kwf_Component_Events_ViewCache extends Kwf_Component_Events
{
    private $_updates = array();

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Row_UpdatesFinished',
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
            'event' => 'Kwf_Component_Event_ComponentClass_PartialsChanged',
            'callback' => 'onComponentClassPartialsChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClass_PartialChanged',
            'callback' => 'onComponentClassPartialChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClassPage_ContentChanged',
            'callback' => 'onComponentClassPageContentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Media_Changed',
            'callback' => 'onMediaChanged'
        );
        return $ret;
    }

    public function onRowUpdatesFinished(Kwf_Component_Event_Row_UpdatesFinished $event)
    {
        if ($this->_updates) {
            $select = new Kwf_Model_Select();
            $or = array();
            foreach ($this->_updates as $key => $values) {
                if ($key === 'db_id') {
                    $or[] = new Kwf_Model_Select_Expr_And(array(
                        new Kwf_Model_Select_Expr_Equal('db_id', array_unique($values)),
                        new Kwf_Model_Select_Expr_Equal('type', 'component'),
                    ));
                } else if ($key === 'master-db_id') {
                    $or[] = new Kwf_Model_Select_Expr_And(array(
                        new Kwf_Model_Select_Expr_Equal('db_id', array_unique($values)),
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
                    $or[] = new Kwf_Model_Select_Expr_And($and);
                }
            }
            $select->where(new Kwf_Model_Select_Expr_Or($or));
            Kwf_Component_Cache::getInstance()->deleteViewCache($select);
            $this->_updates = array();
        }
    }

    public function onContentChange(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $this->_updates['db_id'][] = $event->dbId;
        $this->_log("db_id=$event->dbId type=component");
    }

    //usually child componets can be deleted using %, but not those from pages table as the ids always start with numeric
    //this method returns all child ids needed for deleting recursively
    private function _getIdsFromRecursiveEvent(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $changedComponent = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId, array('ignoreVisible'=>true));
        $ids = array($changedComponent->getPageOrRoot()->componentId);
        foreach (Kwf_Component_Data_Root::getInstance()->getPageGenerators() as $gen) {
            $c = $changedComponent;
            while ($c && !$c->isPage && !$c instanceof Kwf_Component_Data_Root && $c->componentClass !== $gen->getClass()) {
                $c = $c->parent;
            }
            if ($c) {
                $ids = array_merge($ids, $gen->getVisiblePageChildIds($c->dbId));
            }
        }
        return $ids;
    }

    public function onRecursiveContentChange(Kwf_Component_Event_Component_RecursiveContentChanged $event)
    {
        foreach ($this->_getIdsFromRecursiveEvent($event) as $id) {
            $this->_updates[] = array(
                'type' => 'component',
                'component_id' => $id . '%',
                'component_class' => $event->class
            );
            $this->_log("type=component component_id=$id% component_class=$event->class");
        }
    }

    public function onMasterContentChange(Kwf_Component_Event_Component_MasterContentChanged $event)
    {
        $this->_updates['master-db_id'][] = $event->dbId;
        $this->_log("db_id=$event->dbId type=master");
    }

    public function onRecursiveMasterContentChange(Kwf_Component_Event_Component_RecursiveMasterContentChanged $event)
    {
        foreach ($this->_getIdsFromRecursiveEvent($event) as $id) {
            $this->_updates[] = array(
                'type' => 'master',
                'component_id' => $id . '%',
            );
            $this->_log("component_id=$id% type=master");
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
    public function onPageChanged(Kwf_Component_Event_Page_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'db_id' => $event->dbId
        );
        $this->_log("type=componentLink db_id=$event->dbId");
    }

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        foreach ($this->_getIdsFromRecursiveEvent($event) as $id) {
            $this->_updates[] = array(
                'type' => 'componentLink',
                'component_id' => $id . '%'
            );
            $this->_log("type=componentLink component_id=$id%");
        }
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        $changedComponent = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId, array('ignoreVisible'=>true));
        $changedChildIdPostfix = substr($changedComponent->componentId, strlen($changedComponent->getPageOrRoot()->componentId));
        foreach ($this->_getIdsFromRecursiveEvent($event) as $id) {
            if ($changedChildIdPostfix) {
                if (is_numeric($id)) {
                    $pattern = $id . $changedChildIdPostfix . '%';
                } else {
                                    //plus child pages not generated by CategoryGenerator
                    $pattern = $id . '%' . $changedChildIdPostfix . '%';
                }
            } else {
                $pattern = $id . '%';
            }
            $this->_updates[] = array(
                //remove all types
                'component_id' => $pattern
            );
            $this->_log("component_id=$pattern");
        }
    }

    public function onComponentClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'component_class' => $event->class
        );
        $this->_log("type=component component_class=$event->class");
    }

    public function onComponentClassPartialsChanged(Kwf_Component_Event_ComponentClass_PartialsChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'partial',
            'component_class' => $event->class
        );
        $this->_log("type=partial component_class=$event->class");
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

    public function onComponentClassPageContentChanged(Kwf_Component_Event_ComponentClassPage_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'page_db_id' => $event->pageDbId,
            'component_class' => $event->class
        );
        $this->_log("type=component page_db_id=$event->pageDbId component_class=$event->class");
    }

    private function _log($msg)
    {
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear $msg", Zend_Log::INFO);
        }
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        Kwf_Media::clearCache($event->class, $event->componentId, $event->type);
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("media cache clear class=$event->class id=$event->componentId type=$event->type", Zend_Log::INFO);
        }
    }
}
