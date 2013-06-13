<?php
class Kwc_Calendar_Directory_CalendarView_Events extends Kwc_Directories_List_View_Events
{
    public function onDirectoryRowInsert(Kwc_Directories_List_EventItemInserted $event)
    {
        $this->_handleEvent($event);
    }

    public function onDirectoryRowDelete(Kwc_Directories_List_EventItemDeleted $event)
    {
        $this->_handleEvent($event);
    }

    public function onDirectoryRowUpdate(Kwc_Directories_List_EventItemUpdated $event)
    {
        $this->_handleEvent($event);
    }

    private function _handleEvent(Kwc_Directories_List_EventItemAbstract $event)
    {
        $row = Kwf_Model_Abstract::getInstance('Kwc_Calendar_Directory_Model')
            ->getRow($event->itemId);
        $date = new Kwf_Date($row->from);
        $partialId = $date->format('Ymd');
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_PartialChanged(
            $this->_class,
            'a'.$partialId
        ));
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_PartialChanged(
            $this->_class,
            'b'.$partialId
        ));
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_PartialChanged(
            $this->_class,
            'c'.$partialId
        ));
    }
}
