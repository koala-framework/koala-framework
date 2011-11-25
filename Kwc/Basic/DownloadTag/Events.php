<?php
class Kwc_Basic_DownloadTag_Events extends Kwc_Abstract_Events
{
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdateNotVisible($c, $event);
        if ($event->isDirty('kwf_upload_id')) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $c->componentId
            ));
        }
    }
}
