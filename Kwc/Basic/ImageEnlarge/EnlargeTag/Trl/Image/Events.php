<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Image_Events extends Kwc_Abstract_Image_Trl_Image_Events
{
    //gets called when own row gets updated, weather component is visible or not
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        if ($event->isDirty(array('kwf_upload_id'))) {
            $this->fireEvent(new Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_AlternativePreviewChangedEvent(
                $c->parent->componentClass, $c->parent
            ));
        }
    }
}
