<?php
class Kwc_Chained_Cc_Events extends Kwc_Chained_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');

        //component events
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_ContentWidthChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_MasterContentChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_ContentChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_PositionChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_ShowInMenuChanged',
            'callback' => 'onComponentChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onComponentChanged'
        );

        //component recursive events
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_RecursiveContentChanged',
            'callback' => 'onComponentRecursiveChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_RecursiveContentWidthChanged',
            'callback' => 'onComponentRecursiveChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_RecursiveHasContentChanged',
            'callback' => 'onComponentRecursiveChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_RecursiveMasterContentChanged',
            'callback' => 'onComponentRecursiveChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onComponentRecursiveChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
            'callback' => 'onComponentRecursiveChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onComponentRecursiveChanged'
        );

        //compoenntClass events
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
            'callback' => 'onComponentClassChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_ComponentClass_MasterContentChanged',
            'callback' => 'onComponentClassChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_ComponentClass_PartialsChanged',
            'callback' => 'onComponentClassChanged'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_ComponentClass_PartialChanged',
            'callback' => 'onComponentClassPartialChanged'
        );

        //other events
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentChangedWithFlag'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentChangedWithFlag'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onComponentChangedWithFlag'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onComponentChangedWithFlag'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_PositionChanged',
            'callback' => 'onComponentChanged'
        );

        //page parentChanged event
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageParentChanged'
        );
        return $ret;
    }

    public function onComponentChangedWithFlag(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        $chainedType = 'Cc';
        $select = array('ignoreVisible'=>true);
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, $chainedType, $select);
        foreach ($chained as $i) {
            $eventCls = get_class($event);
            $this->fireEvent(
                new $eventCls($this->_class, $i, $event->flag)
            );
        }
    }

    public function onComponentChanged(Kwf_Component_Event_Component_Abstract $event)
    {
        $chainedType = 'Cc';
        $select = array('ignoreVisible'=>true);
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, $chainedType, $select);
        foreach ($chained as $i) {
            $eventCls = get_class($event);
            $this->fireEvent(
                new $eventCls($this->_class, $i)
            );
        }
    }

    public function onComponentRecursiveChanged(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $chainedType = 'Cc';
        $select = array('ignoreVisible'=>true);
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, $chainedType, $select);
        foreach ($chained as $i) {
            $eventCls = get_class($event);
            $this->fireEvent(
                new $eventCls($this->_class, $i)
            );
        }
    }

    public function onComponentClassChanged(Kwf_Component_Event_ComponentClass_Abstract $event)
    {
        $eventCls = get_class($event);
        if ($event->subroot) {
            $chainedType = 'Cc';
            $select = array('ignoreVisible'=>true);
            $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->subroot, $chainedType, $select);
            foreach ($chained as $component) {
                $this->fireEvent(
                    new $eventCls($this->_class, $component)
                );
            }
        } else {
            $this->fireEvent(
                new $eventCls($this->_class)
            );
        }
    }

    public function onComponentClassPartialChanged(Kwf_Component_Event_ComponentClass_PartialChanged $event)
    {
        $eventCls = get_class($event);
        $this->fireEvent(
            new $eventCls($this->_class, $event->id)
        );
    }

    public function onPageParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $chainedType = 'Cc';
        $select = array('ignoreVisible'=>true);
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, $chainedType, $select);
        foreach ($chained as $i) {
            $newParent = Kwc_Chained_Abstract_Component::getChainedByMaster($event->newParent, $i, $chainedType, $select);
            $oldParent = Kwc_Chained_Abstract_Component::getChainedByMaster($event->oldParent, $i, $chainedType, $select);
            $eventCls = get_class($event);
            $this->fireEvent(
                new $eventCls($this->_class, $i, $newParent, $oldParent)
            );
        }
    }
}
