<?php
class Kwc_Directories_TopChoose_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Directories_TopChoose_Model',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdated'
        );
        return $ret;
    }

    public function onRowUpdated(Kwf_Events_Event_Row_Updated $event)
    {
        if ($event->isDirty('directory_component_id')) {
            $this->fireEvent(new Kwc_Directories_List_EventDirectoryChanged($this->_class));
        }
    }
}
