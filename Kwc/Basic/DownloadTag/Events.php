<?php
class Kwc_Basic_DownloadTag_Events extends Kwc_Basic_LinkTag_Abstract_Events
{
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdateNotVisible($c, $event);
        if ($event->isDirty(array('kwf_upload_id', 'content_disposition', 'rel_nofollow'))) {
            $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                $this->_class, $c
            ));
        }
        if ($event->isDirty('filename') || $event->isDirty('kwf_upload_id')) {
            $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                $this->_class, $c
            ));
        }
    }
}
