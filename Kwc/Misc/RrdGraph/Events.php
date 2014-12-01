<?php
class Kwc_Misc_RrdGraph_Events extends Kwc_Abstract_Events
{
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdateNotVisible($c, $event);
        $this->fireEvent(new Kwf_Events_Event_Media_Changed(
            $this->_class, $c
        ));
    }
}
