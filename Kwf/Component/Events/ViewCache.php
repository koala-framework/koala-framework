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
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_FilenameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onPageRecursiveUrlChanged'
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
                    $or[] = new Kwf_Model_Select_Expr_Equal($key, array_unique($values));
                } else {
                    $and = array();
                    foreach ($values as $k => $v) {
                        if (strpos($v, '%') !== false) {
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
    }

    public function onRecursiveContentChange(Kwf_Component_Event_Component_RecursiveContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'component_id' => $event->componentId . '%',
            'component_class' => $event->class
        );
    }

    public function onPageChanged(Kwf_Component_Event_Page_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'db_id' => $event->dbId
        );
    }

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'component_id' => $event->componentId . '%'
        );
    }

    public function onComponentClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'component',
            'component_class' => $event->class
        );
    }

    public function onComponentClassPartialsChanged(Kwf_Component_Event_ComponentClass_PartialsChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'partial',
            'component_class' => $event->class
        );
    }

    public function onComponentClassPartialChanged(Kwf_Component_Event_ComponentClass_PartialChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'partial',
            'component_class' => $event->class,
            'value' => $event->id
        );
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        Kwf_Media::getOutputCache()->remove(Kwf_Media::createCacheId(
            $event->class, $event->componentId, $event->type
        ));
    }
}
