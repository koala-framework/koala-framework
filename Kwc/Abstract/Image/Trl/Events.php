<?php
class Kwc_Abstract_Image_Trl_Events extends Kwc_Abstract_Composite_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $childImage = Kwc_Abstract::getChildComponentClass($this->_class, 'image');
        $ret[] = array(
            'class' => $childImage,
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onOwnImageHasContentChanged'
        );
        return $ret;
    }

    public function onOwnImageHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        if ($event->component->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->component->parent
            ));
        }
    }
}
