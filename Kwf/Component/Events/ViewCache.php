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
            'event' => 'Kwf_Component_Event_Page_FilenameChanged',
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
                        if (strpos($v, '%') !== false) {
                            $and[] = new Kwf_Model_Select_Expr_Like($k, str_replace('_', '\\_', $v));
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
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear db_id=$event->dbId type=component", Zend_Log::INFO);
        }
    }

    public function onRecursiveContentChange(Kwf_Component_Event_Component_RecursiveContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'component_id' => $event->componentId . '%',
            'component_class' => $event->class
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=component component_id=$event->componentId% component_class=$event->class", Zend_Log::INFO);
        }
    }

    public function onMasterContentChange(Kwf_Component_Event_Component_MasterContentChanged $event)
    {
        $this->_updates['master-db_id'][] = $event->dbId;
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear db_id=$event->dbId type=master", Zend_Log::INFO);
        }
    }

    public function onRecursiveMasterContentChange(Kwf_Component_Event_Component_RecursiveMasterContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'master',
            'component_id' => $event->componentId . '%',
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear component_id=$event->componentId% type=master", Zend_Log::INFO);
        }
    }

    public function onClassMasterContentChange(Kwf_Component_Event_ComponentClass_MasterContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'master',
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=master", Zend_Log::INFO);
        }
    }

    // namechanged and filnamechanged-events
    public function onPageChanged(Kwf_Component_Event_Page_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'db_id' => $event->dbId
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=componentLink db_id=$event->dbId", Zend_Log::INFO);
        }
    }

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'component_id' => $event->componentId . '%'
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=componentLink component_id=$event->componentId%", Zend_Log::INFO);
        }
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        $this->_updates[] = array(
            //remove all types
            'component_id' => $event->componentId . '%'
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear component_id=$event->componentId%", Zend_Log::INFO);
        }
    }

    public function onComponentClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'component_class' => $event->class
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=component component_class=$event->class", Zend_Log::INFO);
        }
    }

    public function onComponentClassPartialsChanged(Kwf_Component_Event_ComponentClass_PartialsChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'partial',
            'component_class' => $event->class
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=partial component_class=$event->class", Zend_Log::INFO);
        }
    }

    public function onComponentClassPartialChanged(Kwf_Component_Event_ComponentClass_PartialChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'partial',
            'component_class' => $event->class,
            'value' => $event->id
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=partial component_class=$event->class value=$event->id", Zend_Log::INFO);
        }
    }

    public function onComponentClassPageContentChanged(Kwf_Component_Event_ComponentClassPage_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'page_db_id' => $event->pageDbId,
            'component_class' => $event->class
        );
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear type=component page_db_id=$event->pageDbId component_class=$event->class", Zend_Log::INFO);
        }
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        Kwf_Media::getOutputCache()->remove(Kwf_Media::createCacheId(
            $event->class, $event->componentId, $event->type
        ));
        $log = Kwf_Component_Events_Log::getInstance();
        if ($log) {
            $log->log("media cache clear class=$event->class id=$event->componentId type=$event->type", Zend_Log::INFO);
        }
    }
}
