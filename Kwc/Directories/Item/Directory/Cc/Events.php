<?php
class Kwc_Directories_Item_Directory_Cc_Events extends Kwc_Abstract_Composite_Cc_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();

        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemUpdated',
            'callback' => 'onMasterChildRowUpdate'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemInserted',
            'callback' => 'onMasterChildRowInsert'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemDeleted',
            'callback' => 'onMasterChildRowDelete'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemsUpdated',
            'callback' => 'onMasterChildModelUpdated'
        );

        return $ret;
    }

    public function onMasterChildRowUpdate(Kwc_Directories_List_EventItemUpdated $event)
    {
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->subroot, 'Cc', array()) as $sr) {
            $this->fireEvent(new Kwc_Directories_List_EventItemUpdated($this->_class, $event->itemId, $sr));
        }
    }

    public function onMasterChildRowInsert(Kwc_Directories_List_EventItemInserted $event)
    {
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->subroot, 'Cc', array()) as $sr) {
            $this->fireEvent(new Kwc_Directories_List_EventItemInserted($this->_class, $event->itemId, $sr));
        }
    }

    public function onMasterChildRowDelete(Kwc_Directories_List_EventItemDeleted $event)
    {
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->subroot, 'Cc', array()) as $sr) {
            $this->fireEvent(new Kwc_Directories_List_EventItemDeleted($this->_class, $event->itemId, $sr));
        }
    }

    public function onMasterChildModelUpdated(Kwc_Directories_List_EventItemsUpdated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventItemsUpdated($this->_class, $event->subroot));
    }

}
