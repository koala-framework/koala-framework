<?php
class Kwc_Directories_List_View_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();

        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (in_array('Kwc_Directories_List_Component', Kwc_Abstract::getParentClasses($class))) {
                if (Kwc_Abstract::hasChildComponentClass($class, 'child', 'view')
                    && $this->_class == Kwc_Abstract::getChildComponentClass($class, 'child', 'view')
                ) {
                    $directoryClasses = call_user_func(
                        array(strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class, 'getItemDirectoryClasses'), $class
                    );
                    foreach ($directoryClasses as $directoryClass) {
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventRowInserted',
                            'callback' => 'onDirectoryRowInsert'
                        );
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventRowDeleted',
                            'callback' => 'onDirectoryRowDelete'
                        );
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventRowUpdated',
                            'callback' => 'onDirectoryRowUpdate'
                        );
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventModelUpdated',
                            'callback' => 'onDirectoryModelUpdate'
                        );
                    }
                }
            }
        }
        return $ret;
    }

    public function onDirectoryRowInsert(Kwc_Directories_List_EventRowInserted $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class));
    }

    public function onDirectoryRowDelete(Kwc_Directories_List_EventRowDeleted $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class));
    }

    public function onDirectoryRowUpdate(Kwc_Directories_List_EventRowUpdated $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class));
        $partialClass = call_user_func(
            array(strpos($this->_class, '.') ? substr($this->_class, 0, strpos($this->_class, '.')) : $this->_class, 'getPartialClass'), $this->_class
        );
        if (is_instance_of($partialClass, 'Kwf_Component_Partial_Id')) {
            $id = $event->row->{$event->row->getModel()->getPrimaryKey()};
            $this->fireEvent(new Kwf_Component_Event_ComponentClass_PartialChanged($this->_class, $id));
        } else {
            $this->fireEvent(new Kwf_Component_Event_ComponentClass_PartialsChanged($this->_class));
        }
    }

    public function onDirectoryModelUpdate(Kwc_Directories_List_EventModelUpdated $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class));
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_PartialsChanged($this->_class));
    }
}
