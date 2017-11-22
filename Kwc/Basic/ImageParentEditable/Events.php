<?php
class Kwc_Basic_ImageParentEditable_Events extends Kwc_Abstract_Image_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        //find components that can create ourself ($this->_class)
        foreach ($this->_getCreatingClasses($this->_class) as $class) {
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Events_Event_Media_Changed',
                'callback' => 'onMediaChanged'
            );
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_Component_ContentChanged',
                'callback' => 'onContentChanged'
            );
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                'callback' => 'onClassContentChanged'
            );
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwc_Abstract_Image_ImageChangedEvent',
                'callback' => 'onImageChanged'
            );
        }
        return $ret;
    }

    public function onContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $cmps = $event->component->getChildComponents(array('componentClass'=>$this->_class));
        foreach ($cmps as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));
        }
    }

    public function onClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    public function onMediaChanged(Kwf_Events_Event_Media_Changed $event)
    {
        $components = $event->component
            ->getChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }

    public function onImageChanged(Kwc_Abstract_Image_ImageChangedEvent $event)
    {
        $components = $event->component
            ->getChildComponents(array('componentClass' => $this->_class));
        $this->_clearMediaCache($components);
    }

    protected function _clearMediaCache($components)
    {
        if (!is_array($components)) $components = array($components);
        foreach ($components as $component) {
            $imageData = $component->getComponent()->getImageData();
            if ($imageData) {
                $dim = $component->getComponent()->getImageDimensions();
                $typeBase = $component->getComponent()->getBaseType();
                $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
                $types = array();
                foreach ($steps as $step) {
                    $types[] = str_replace('{width}', $step, $typeBase);
                }
                $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                    $this->_class, $component, $types
                ));
            }
        }
    }
}
