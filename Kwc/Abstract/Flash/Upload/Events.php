<?php
class Kwc_Abstract_Flash_Upload_Events extends Kwc_Abstract_Events
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        $reference = $event->row->getModel()->getReference(
            Kwc_Abstract::getSetting($this->_class, 'uploadModelRule')
        );
        if ($event->isDirty($reference['column'])) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $c->componentId
            ));
        }
    }
}
