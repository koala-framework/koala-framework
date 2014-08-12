<?php
class Kwc_Abstract_Image_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $usesContentWidth = false;
        foreach (Kwc_Abstract::getSetting($this->_class, 'dimensions') as $dim) {
            if (isset($dim['width']) && $dim['width'] == Kwc_Abstract_Image_Component::CONTENT_WIDTH) {
                $usesContentWidth = true;
            }
        }
        if ($usesContentWidth) {
            $ret[] = array(
                'event' => 'Kwf_Component_Event_Component_ContentWidthChanged',
                'callback' => 'onContentWidthChanged'
            );
            $ret[] = array(
                'event' => 'Kwf_Component_Event_Component_RecursiveContentWidthChanged',
                'callback' => 'onRecursiveContentWidthChanged'
            );
        }
        return $ret;
    }

    protected function _fireMediaChanged(Kwf_Component_Data $c)
    {
        $imageData = $c->getComponent()->getImageDataOrEmptyImageData();
        $this->fireEvent(new Kwc_Abstract_Image_ImageChangedEvent($this->_class, $c));
        if ($imageData) {
            $typeBase = $c->getComponent()->getBaseType();
            // Kwc_Abstract_Image_Component->getBaseImageUrl is cached in Kwf_Media and uses therefore the base type
            $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                $this->_class, $c, $typeBase
            ));
            $dim = $c->getComponent()->getImageDimensions();
            $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
            foreach ($steps as $step) {
                $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                    $this->_class, $c, str_replace('{width}', $step, $typeBase)
                ));
            }
        }
        $this->fireEvent(new Kwf_Component_Event_Component_ContentWidthChanged(
            $this->_class, $c
        ));
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $c
        ));
    }

    //gets called when own row gets updated, weather component is visible or not
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdateNotVisible($c, $event);
        if ($event->isDirty(array('kwf_upload_id', 'width', 'height',
                'dimension', 'crop_x', 'crop_y',
                'crop_width', 'crop_height'))
        ) {
            $this->_fireMediaChanged($c);
        }
    }

    public function onContentWidthChanged(Kwf_Component_Event_Component_ContentWidthChanged $event)
    {
        $c = $event->component;
        $this->fireEvent(new Kwf_Component_Event_ComponentClassPage_ContentChanged(
            $this->_class, $c->getPageOrRoot()
        ));
    }

    public function onRecursiveContentWidthChanged(Kwf_Component_Event_Component_RecursiveContentWidthChanged $event)
    {
        $c = $event->component;
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $c
        ));
    }
}
