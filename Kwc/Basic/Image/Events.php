<?php
class Kwc_Basic_Image_Events extends Kwc_Abstract_Image_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        if (Kwc_Abstract::hasSetting($this->_class, 'useParentImage')) {
            $ret[] = array(
                'event' => 'Kwf_Component_Event_Media_Changed',
                'callback' => 'onMediaChanged'
            );
        }
        return $ret;
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($event->componentId, array('ignoreVisible'=>true))
            ->getChildComponents(array('componentClass' => $this->_class));
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
