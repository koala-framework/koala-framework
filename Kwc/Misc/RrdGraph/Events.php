<?php
class Kwc_Misc_RrdGraph_Events extends Kwc_Abstract_Events
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        $this->fireEvent(new Kwf_Component_Event_Media_Changed(
            $this->_class, $c->componentId
        ));
    }
}
