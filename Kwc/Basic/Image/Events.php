<?php
class Kwc_Basic_Image_Events extends Kwc_Abstract_Image_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        if (Kwc_Abstract::hasSetting($this->_class, 'useParentImage')) {
            //find components that can create ourself ($this->_class)
            foreach (Kwc_Abstract::getComponentClasses() as $class) {
                if (in_array('Kwc_Abstract_Image_Component', Kwc_Abstract::getParentClasses($class))) {
                    if (Kwc_Abstract::getChildComponentClasses($class, array('componentClass'=>$this->_class))) {

                        $ret[] = array(
                            'class' => $class,
                            'event' => 'Kwf_Component_Event_Media_Changed',
                            'callback' => 'onMediaChanged'
                        );
                        $ret[] = array(
                            'class' => $class,
                            'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                            'callback' => 'onClassContentChanged'
                        );

                    }
                }
            }
        }
        return $ret;
    }

    public function onClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $components = $event->component
            ->getChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $this->_fireMediaChanged($component);
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }
}
