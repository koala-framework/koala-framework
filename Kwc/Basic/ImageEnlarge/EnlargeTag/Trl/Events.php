<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Events extends Kwc_Chained_Trl_Events
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
            if (is_instance_of($class, 'Kwc_Basic_ImageEnlarge_Trl_Component') && $this->_canCreateUsIndirectly($class)) {
                $imageClass = Kwc_Abstract::getChildComponentClass($class, 'image');
                $ret[] = array(
                    'class' => $imageClass,
                    'event' => 'Kwc_Abstract_Image_ImageChangedEvent',
                    'callback' => 'onImageChanged'
                );
                $ret[] = array(
                    'class' => $imageClass,
                    'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                    'callback' => 'onClassContentChanged'
                );

                $masterComponentClass = Kwc_Abstract::getSetting($class, 'masterComponentClass');
                $ret[] = array(
                    'class' => $masterComponentClass,
                    'event' => 'Kwc_Abstract_Image_ImageChangedEvent',
                    'callback' => 'onMasterImageChanged'
                );
                $ret[] = array(
                    'class' => $masterComponentClass,
                    'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                    'callback' => 'onClassContentChanged'
                );

                $ret[] = array(
                    'class' => $class,
                    'event' => 'Kwf_Component_Event_Component_ContentChanged',
                    'callback' => 'onImageContentChanged'
                );
                $ret[] = array(
                    'class' => $class,
                    'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                    'callback' => 'onClassContentChanged'
                );
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

    public function onImageContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $childComponents = $event->component->getChildComponents(array(
            'componentClass' => $this->_class
        ));
        foreach ($childComponents as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));

            $imageData = $c->getComponent()->getImageData();
            if ($imageData) {
                $dim = $c->getComponent()->getImageDimensions();
                $typeBase = $c->getComponent()->getBaseType();
                $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
                foreach ($steps as $step) {
                    $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                        $this->_class, $c, str_replace('{width}', $step, $typeBase)
                    ));
                }
            }
        }
    }

    public function onImageChanged(Kwc_Abstract_Image_ImageChangedEvent $event)
    {
        $components = $event->component->parent
            ->getRecursiveChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $imageData = $component->getComponent()->getImageData();
            if ($imageData) {
                $dim = $component->getComponent()->getImageDimensions();
                $typeBase = $component->getComponent()->getBaseType();
                $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
                foreach ($steps as $step) {
                    $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                        $this->_class, $component, str_replace('{width}', $step, $typeBase)
                    ));
                }
            }
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }

    public function onMasterImageChanged(Kwc_Abstract_Image_ImageChangedEvent $event)
    {
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl');
        foreach ($chained as $c) {
            $components = $c->getRecursiveChildComponents(array('componentClass' => $this->_class));
            foreach ($components as $component) {
                $imageData = $component->getComponent()->getImageData();
                $dim = $component->getComponent()->getImageDimensions();
                $typeBase = $component->getComponent()->getBaseType();
                $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
                foreach ($steps as $step) {
                    $this->fireEvent(new Kwf_Events_Event_Media_Changed(
                        $this->_class, $component, str_replace('{width}', $step, $typeBase)
                    ));
                }
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $component
                ));
            }
        }
    }
}
