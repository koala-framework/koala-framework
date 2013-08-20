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
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'),
            'event' => 'Kwf_Component_Event_Media_Changed',
            'callback' => 'onMasterMediaChanged'
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

    public function onMasterMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl') as $c) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $c, $event->type
            ));
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));
        }
    }
}
