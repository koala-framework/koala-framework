<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Events extends Kwc_Abstract_Image_Trl_Events
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
                    'event' => 'Kwf_Component_Event_Media_Changed',
                    'callback' => 'onMediaChanged'
                );
                $ret[] = array(
                    'class' => $imageClass,
                    'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                    'callback' => 'onClassContentChanged'
                );

                $masterComponentClass = Kwc_Abstract::getSetting($class, 'masterComponentClass');
                $ret[] = array(
                    'class' => $masterComponentClass,
                    'event' => 'Kwf_Component_Event_Media_Changed',
                    'callback' => 'onMasterMediaChanged'
                );
                $ret[] = array(
                    'class' => $masterComponentClass,
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

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $components = $event->component->parent
            ->getRecursiveChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $component
            ));
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }

    public function onMasterMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl');
        foreach ($chained as $c) {
            $components = $c->getRecursiveChildComponents(array('componentClass' => $this->_class));
            foreach ($components as $component) {
                $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                    $this->_class, $component
                ));
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $component
                ));
            }
        }
    }

    //gets called when own row gets updated, weather component is visible or not
    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        //don't call parent, as it would fire Media_Changed which we don't need in that case (as our own image in the alternative preview image)
        if ($event->isDirty(array('own_image'))) {
            $this->fireEvent(new Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_AlternativePreviewChangedEvent(
                $this->_class, $c
            ));
        }
    }
}
