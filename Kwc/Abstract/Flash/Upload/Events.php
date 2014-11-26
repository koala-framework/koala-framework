<?php
class Kwc_Abstract_Flash_Upload_Events extends Kwc_Abstract_Events
{
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdateNotVisible($c, $event);
        $reference = $event->row->getModel()->getReference(
            Kwc_Abstract::getSetting($this->_class, 'uploadModelRule')
        );
        if ($event->isDirty($reference['column'])) {
            $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                $this->_class, $c
            ));
        }
    }
}
