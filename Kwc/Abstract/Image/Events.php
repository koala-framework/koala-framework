<?php
class Kwc_Abstract_Image_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $usesContentWidth = false;
        foreach (Kwc_Abstract::getSetting($this->_class, 'dimensions') as $dim) {
            if ($dim['width'] == Kwc_Abstract_Image_Component::CONTENT_WIDTH) {
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
        $type = $c->getComponent()->getImageUrlType();
        $this->fireEvent(new Kwf_Component_Event_Media_Changed(
            $this->_class, $c, $type
        ));
        $this->fireEvent(new Kwf_Component_Event_Media_Changed(
            $this->_class, $c, 'dpr2-'.$type
        ));
        $this->fireEvent(new Kwf_Component_Event_Component_ContentWidthChanged(
            $this->_class, $c
        ));
    }

    //gets called when own row gets updated, weather component is visible or not
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdateNotVisible($c, $event);
        if ($event->isDirty(array('kwf_upload_id', 'width', 'height', 'dimension'))) {
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
