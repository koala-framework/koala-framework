<?php
class Vpc_Basic_Image_Events extends Vpc_Abstract_Image_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        if (Vpc_Abstract::hasSetting($this->_class, 'useParentImage')) {
            $ret[] = array(
                'event' => 'Vps_Component_Event_Media_Changed',
                'callback' => 'onMediaChanged'
            );
        }
        return $ret;
    }

    public function onMediaChanged(Vps_Component_Event_Media_Changed $event)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($event->componentId);
        $components = $c->getChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $this->fireEvent(new Vps_Component_Event_Media_Changed(
                $this->_class, $component->componentId
            ));
        }
    }
}
