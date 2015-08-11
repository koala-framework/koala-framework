<?php
class Kwc_Basic_ParentContent_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageParentChanged'
        );
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            $classWithoutPoint = $class;
            if (($pos = strpos($class, '.')) !== false) $classWithoutPoint = substr($class, 0, $pos);
            if (Kwc_Abstract::getFlag($class, 'hasAlternativeComponent') &&
                in_array($this->_class, call_user_func(array($classWithoutPoint, 'getAlternativeComponents'), $class))
            ) {
                $ret[] = array(
                    'class' => $class,
                    'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                    'callback' => 'onParentHasContentChanged'
                );
                $ret[] = array(
                    'class' => $class,
                    'event' => 'Kwf_Component_Event_Component_RecursiveHasContentChanged',
                    'callback' => 'onParentRecursiveHasContentChanged'
                );
            }
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $generator) {
                if ($generator['class'] == 'Kwf_Component_Generator_Box_StaticSelect') {
                    foreach ($generator['component'] as $componentClass) {
                        if ($componentClass == $this->_class) continue;
                        $ret[] = array(
                                'class' => $componentClass,
                                'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                                'callback' => 'onParentHasContentChanged'
                        );
                        $ret[] = array(
                                'class' => $componentClass,
                                'event' => 'Kwf_Component_Event_Component_RecursiveHasContentChanged',
                                'callback' => 'onParentRecursiveHasContentChanged'
                        );
                    }
                }
            }
        }
        return $ret;
    }

    public function onPageParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $event->component
        ));
    }

    public function onParentHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $pc = $event->component;
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveHasContentChanged(
            $this->_class, $pc
        ));
    }

    public function onParentRecursiveHasContentChanged(Kwf_Component_Event_Component_RecursiveHasContentChanged $event)
    {
        $pc = $event->component;
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveHasContentChanged(
            $this->_class, $pc
        ));
    }


}
