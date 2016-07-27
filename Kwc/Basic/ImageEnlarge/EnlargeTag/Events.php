<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Events extends Kwc_Abstract_Events
{
    private function _canCreateUsIndirectly($class)
    {
        static $cache = array();
        $cacheId = $this->_class.'-'.$class;
        if (isset($cache[$cacheId])) return $cache[$cacheId];
        foreach (Kwc_Abstract::getChildComponentClasses($class, array('generatorFlags'=>array('static'=>true))) as $c) {
            if ($c == $this->_class) {
                $cache[$cacheId] = true;
                return true;
            }
            if ($this->_canCreateUsIndirectly($c)) {
                return true;
            }
        }
        $cache[$cacheId] = false;
        return false;
    }

    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if ($this->_canCreateUsIndirectly($class)) {
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
        }
        return $ret;
    }

    public function onImageChanged(Kwc_Abstract_Image_ImageChangedEvent $event)
    {
        $components = $event->component
            ->getRecursiveChildComponents(array('componentClass' => $this->_class, 'ignoreVisible'=>true)); //ignore visible because we need to clear media cache for invisible images too (as it's shown in preview)
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
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }

    public function onClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }
}
