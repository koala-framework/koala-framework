<?php
class Vps_Component_Events_ViewCache extends Vps_Component_Events
{
    private $_updatedDbIds = array();

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Vps_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChange'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Row_UpdatesFinished',
            'callback' => 'onRowUpdatesFinished'
        );
        return $ret;
    }

    public function onContentChange(Vps_Component_Event_Component_ContentChanged $event)
    {
        $this->_updatedDbIds[] = $event->dbId;
    }

    public function onRowUpdatesFinished(Vps_Component_Event_Row_UpdatesFinished $event)
    {
        if ($this->_updatedDbIds) {
            $select = new Vps_Model_Select();
            $select->whereEquals('db_id', array_unique($this->_updatedDbIds));
            Vps_Component_Cache::getInstance()->deleteViewCache($select);
        }
    }
}
