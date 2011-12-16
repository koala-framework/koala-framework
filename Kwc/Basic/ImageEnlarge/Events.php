<?php
class Kwc_Basic_ImageEnlarge_Events extends Kwc_Basic_Image_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_AlternativePreviewChangedEvent',
            'callback' => 'onAlternativePreviewChanged'
        );
        return $ret;
    }

    public function onAlternativePreviewChanged(Kwc_Basic_ImageEnlarge_EnlargeTag_AlternativePreviewChangedEvent $event)
    {
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($event->componentId, array('ignoreVisible'=>true));
        $component = $component->parent;
        if (is_instance_of($component->componentClass, 'Kwc_Basic_LinkTag_Component')) {
            $component = $component->parent;
        }
        if ($component->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $component->componentId
            ));
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component->dbId
            ));
        }
    }
}
