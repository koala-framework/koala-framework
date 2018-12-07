<?php
class Kwc_Directories_TopChoose_Events extends Kwc_Abstract_Events
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        if ($event->isDirty('directory_component_id')) {
            $subroots = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByDbId($event->row->directory_component_id, array('ignoreVisible'=>true));
            foreach ($subroots as $subroot) {
                $this->fireEvent(new Kwc_Directories_List_EventDirectoryChanged($this->_class, $subroot));
            }
        }
    }
}
