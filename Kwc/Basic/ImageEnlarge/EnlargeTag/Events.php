<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Events extends Kwc_Abstract_Image_Events
{
    private function _canCreateUsIndirectly($class)
    {
        foreach (Kwc_Abstract::getChildComponentClasses($class, array('generatorFlags'=>array('static'=>true))) as $c) {
            if ($c == $this->_class) {
                return true;
            }
            if ($this->_canCreateUsIndirectly($c)) {
                return true;
            }
        }
        return false;
    }

    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if ($this->_canCreateUsIndirectly($class)) {
                $ret[] = array(
                    'class' => $class,
                    'event' => 'Kwf_Component_Event_Media_Changed',
                    'callback' => 'onMediaChanged'
                );
            }
        }
        return $ret;
    }

    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        //don't call parent, as it would fire Media_Changed which we don't need in that case (as our own image in the alternative preview image)
        if ($event->isDirty(array('width', 'height', 'dimension'))) {
            parent::_onOwnRowUpdateNotVisible($c, $event);
        }
        if ($event->isDirty(array('kwf_upload_id', 'preview_image'))) {
            $this->fireEvent(new Kwc_Basic_ImageEnlarge_EnlargeTag_AlternativePreviewChangedEvent(
                $this->_class, $c->componentId
            ));
        }
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($event->componentId, array('ignoreVisible'=>true))
            ->getRecursiveChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $component->componentId
            ));
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component->dbId
            ));
        }
    }
}
