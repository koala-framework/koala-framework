<?php
class Kwc_Directories_Item_Directory_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $generator = Kwf_Component_Generator_Abstract::getInstance($this->_class, 'detail');
        $ret[] = array(
            'class' => get_class($generator->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onChildRowUpdate'
        );
        $ret[] = array(
            'class' => get_class($generator->getModel()),
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onChildRowInsert'
        );
        $ret[] = array(
            'class' => get_class($generator->getModel()),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onChildRowDelete'
        );
        $ret[] = array(
            'class' => get_class($generator->getModel()),
            'event' => 'Kwf_Component_Event_Model_Updated',
            'callback' => 'onChildModelUpdated'
        );
        return $ret;
    }

    public function onChildRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventRowUpdated($this->_class, $event->row->{$event->row->getModel()->getPrimaryKey()}));
    }

    public function onChildRowInsert(Kwf_Component_Event_Row_Inserted $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventRowInserted($this->_class, $event->row->{$event->row->getModel()->getPrimaryKey()}));
    }

    public function onChildRowDelete(Kwf_Component_Event_Row_Deleted $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventRowDeleted($this->_class, $event->row->{$event->row->getModel()->getPrimaryKey()}));
    }

    public function onChildModelUpdated(Kwf_Component_Event_Model_Updated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventModelUpdated($this->_class));
    }
}
