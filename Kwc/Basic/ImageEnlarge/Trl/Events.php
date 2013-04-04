<?php
class Kwc_Basic_ImageEnlarge_Trl_Events extends Kwc_Abstract_Image_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_AlternativePreviewChangedEvent',
            'callback' => 'onAlternativePreviewChanged'
        );
        return $ret;
    }

    public function onAlternativePreviewChanged(Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_AlternativePreviewChangedEvent $event)
    {
        $component = $event->component->parent;
        if (is_instance_of($component->componentClass, 'Kwc_Basic_LinkTag_Trl_Component')) {
            $component = $component->parent;
        }
        if ($component->componentClass == $this->_class) {
            //media changed in image child component
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }
}
