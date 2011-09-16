<?php
class Vps_Component_Events_ViewCache extends Vps_Component_Events
{
    private $_updates = array();

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Vps_Component_Event_Row_UpdatesFinished',
            'callback' => 'onRowUpdatesFinished'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChange'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Page_NameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Page_FilenameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Page_ParentChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Page_RecursiveFilenameChanged',
            'callback' => 'onPageRecursiveChanged'
        );
        return $ret;
    }

    public function onRowUpdatesFinished(Vps_Component_Event_Row_UpdatesFinished $event)
    {
        if ($this->_updates) {
            $select = new Vps_Model_Select();
            $or = array();
            foreach ($this->_updates as $key => $values) {
                if (is_string($key)) {
                    $or[] = new Vps_Model_Select_Expr_Equal($key, array_unique($values));
                } else {
                    $and = array();
                    foreach ($values as $k => $v) {
                        if (strpos($v, '%') !== false) {
                            $and[] = new Vps_Model_Select_Expr_Like($k, $v);
                        } else {
                            $and[] = new Vps_Model_Select_Expr_Equal($k, $v);
                        }
                    }
                    $or[] = new Vps_Model_Select_Expr_And($and);
                }
            }
            $select->where(new Vps_Model_Select_Expr_Or($or));
            Vps_Component_Cache::getInstance()->deleteViewCache($select);
        }
    }

    public function onContentChange(Vps_Component_Event_Component_ContentChanged $event)
    {
        $this->_updates['db_id'][] = $event->dbId;
    }

    public function onPageChanged(Vps_Component_Event_Page_ContentChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'db_id' => $event->dbId
        );
    }

    public function onPageRecursiveChanged(Vps_Component_Event_Page_RecursiveFilenameChanged $event)
    {
        $this->_updates[] = array(
            'type' => 'componentLink',
            'component_id' => $event->componentId . '%'
        );
    }
}
