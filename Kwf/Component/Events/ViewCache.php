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
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_FilenameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveFilenameChanged',
            'callback' => 'onPageRecursiveFilenameChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageParentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Media_Changed',
            'callback' => 'onMediaChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
            'callback' => 'onComponentClassContentChange'
        );
        return $ret;
    }

    public function onRowUpdatesFinished(Kwf_Component_Event_Row_UpdatesFinished $event)
    {
        if ($this->_updates) {
            $select = new Kwf_Model_Select();
            $or = array();
            foreach ($this->_updates as $key => $values) {
                if (is_string($key)) {
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

    public function onComponentClassContentChange(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->_updates['component_class'][] = $event->class;
    }

    public function onPageChanged(Kwf_Component_Event_Page_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'db_id' => $event->dbId
        );
    }

    public function onPageRecursiveFilenameChanged(Kwf_Component_Event_Page_RecursiveFilenameChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'component_id' => $event->componentId . '%'
        );
    }

    public function onPageParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'component_id' => $event->dbId . '%'
        );
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        Kwf_Media::getOutputCache()->remove(Kwf_Media::createCacheId(
            $event->class, $event->componentId, $event->type
        ));
    }
}
