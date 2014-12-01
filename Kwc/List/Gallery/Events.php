<?php
class Kwc_List_Gallery_Events extends Kwc_Abstract_List_Events
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        if ($event->isDirty('columns')) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentWidthChanged(
                $this->_class, $c
            ));
        }
    }

}
