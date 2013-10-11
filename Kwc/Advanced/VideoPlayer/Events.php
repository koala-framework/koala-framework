<?php
class Kwc_Advanced_VideoPlayer_Events extends Kwc_Abstract_Events
{
    protected function _fireMediaChanged(Kwf_Component_Data $c, $type)
    {
        $this->fireEvent(new Kwf_Component_Event_Media_Changed(
            $this->_class, $c, $type
        ));
    }

    //gets called when own row gets updated, weather component is visible or not
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdateNotVisible($c, $event);
        if ($event->isDirty(array('video_width', 'video_height'))) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentWidthChanged(
                $this->_class, $c
            ));
        }
        if($event->isDirty(array('mp4_kwf_upload_id'))) {
            $this->_fireMediaChanged($c, 'mp4');
        }
        if($event->isDirty(array('ogg_kwf_upload_id'))) {
            $this->_fireMediaChanged($c, 'ogg');
        }
        if($event->isDirty(array('webm_kwf_upload_id'))) {
            $this->_fireMediaChanged($c, 'webm');
        }
        //content changed
        foreach (Kwf_Component_Data_Root::getInstance()
            ->getComponentsByDbId($event->row->component_id) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));
        }
    }
}
