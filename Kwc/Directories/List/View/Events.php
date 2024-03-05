<?php
class Kwc_Directories_List_View_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();

        $processedDirectories = array();
        $processedDetails = array();

        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (in_array('Kwc_Directories_List_Component', Kwc_Abstract::getParentClasses($class)) ||
                in_array('Kwc_Directories_List_Trl_Component', Kwc_Abstract::getParentClasses($class)) ||
                in_array('Kwc_Directories_List_Cc_Component', Kwc_Abstract::getParentClasses($class))
            ) {
                if (Kwc_Abstract::hasChildComponentClass($class, 'child', 'view')
                    && $this->_class == Kwc_Abstract::getChildComponentClass($class, 'child', 'view')
                ) {
                    $directoryClasses = call_user_func(
                        array(strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class, 'getItemDirectoryClasses'), $class
                    );
                    foreach ($directoryClasses as $directoryClass) {
                        if (in_array($directoryClass, $processedDirectories)) {
                            //add only once
                            continue;
                        }
                        $processedDirectories[] = $directoryClass;
                        foreach (Kwc_Abstract::getChildComponentClasses($directoryClass, 'detail') as $detailClass) {
                            if (in_array($detailClass, $processedDetails)) {
                                //add only once
                                continue;
                            }
                            $processedDetails[] = $detailClass;
                            $ret[] = array(
                                'class' => $detailClass,
                                'event' => 'Kwf_Component_Event_Component_Added',
                                'callback' => 'onDirectoryDetailAdded'
                            );
                            $ret[] = array(
                                'class' => $detailClass,
                                'event' => 'Kwf_Component_Event_Component_Removed',
                                'callback' => 'onDirectoryDetailRemoved'
                            );
                            $ret[] = array(
                                'class' => $detailClass,
                                'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                                'callback' => 'onDirectoryDetailHasContentChanged'
                            );
                        }

                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventItemUpdated',
                            'callback' => 'onDirectoryRowUpdate'
                        );
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventItemsUpdated',
                            'callback' => 'onDirectoryUpdate'
                        );
                    }
                    $ret[] = array(
                        'class' => $class,
                        'event' => 'Kwc_Directories_List_EventDirectoryChanged',
                        'callback' => 'onDirectoryUpdate'
                    );
                }
            }
        }
        return $ret;
    }

    private function _usesPartialId()
    {
        $partialClass = call_user_func(
            array(strpos($this->_class, '.') ? substr($this->_class, 0, strpos($this->_class, '.')) : $this->_class, 'getPartialClass'), $this->_class
        );
        return is_instance_of($partialClass, 'Kwf_Component_Partial_Id');
    }

    private function _fireTagEvent($event, $directory, $itemId = null)
    {
        $cacheId = 'kwc-dirlistview-isdata-'.$this->_class;
        $dirIs = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if ($dirIs === false) {
            $dirIs = array(
                'data' => false,
                'string' => false
            );
            foreach (Kwc_Abstract::getComponentClasses() as $class) {
                if (in_array('Kwc_Directories_List_Component', Kwc_Abstract::getParentClasses($class)) || in_array('Kwc_Directories_List_Trl_Component', Kwc_Abstract::getParentClasses($class))) {
                    if (Kwc_Abstract::hasChildComponentClass($class, 'child', 'view')
                        && $this->_class == Kwc_Abstract::getChildComponentClass($class, 'child', 'view')
                    ) {
                        $isData = call_user_func(
                            array(strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class, 'getItemDirectoryIsData'), $class
                        );
                        if ($isData) {
                            $dirIs['data'] = true;
                        } else {
                            $dirIs['string'] = true;
                        }
                    }
                }
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $dirIs);
        }

        $event = "Kwf_Component_Event_ComponentClass_Tag_$event";
        if ($itemId) {
            if ($dirIs['data']) {
                $this->fireEvent(new $event($this->_class, $directory->componentId, $itemId));
            }
            if ($dirIs['string']) {
                $this->fireEvent(new $event($this->_class, $directory->componentClass, $itemId));
            }
        } else {
            if ($dirIs['data']) {
                $this->fireEvent(new $event($this->_class, $directory->componentId));
            }
            if ($dirIs['string']) {
                $this->fireEvent(new $event($this->_class, $directory->componentClass));
            }
        }
    }

    public function onDirectoryDetailAdded(Kwf_Component_Event_Component_Added $event)
    {
        $subroot = $event->component->getSubroot();
        $directory = $event->component->parent;
        $this->_fireTagEvent('ContentChanged', $directory);
        $this->_fireTagEvent('PartialsChanged', $directory);
        if (!$this->_usesPartialId()) {
            $this->_fireTagEvent('AllPartialChanged', $directory);
        }
    }

    public function onDirectoryDetailRemoved(Kwf_Component_Event_Component_Removed $event)
    {
        $subroot = $event->component->getSubroot();
        $directory = $event->component->parent;
        $this->_fireTagEvent('ContentChanged', $directory);
        $this->_fireTagEvent('PartialsChanged', $directory);
        if ($this->_usesPartialId()) {
            $this->_fireTagEvent('PartialChanged', $directory, $event->component->id);
        } else {
            $this->_fireTagEvent('AllPartialChanged', $directory);
        }
    }

    public function onDirectoryDetailHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $subroot = $event->component->getSubroot();
        $directory = $event->component->parent;
        if ($this->_usesPartialId()) {
            $this->_fireTagEvent('PartialChanged', $directory, $event->component->id);
        } else {
            $this->_fireTagEvent('AllPartialChanged', $directory);
        }
    }

    public function onDirectoryRowUpdate(Kwc_Directories_List_EventItemUpdated $event)
    {
        $gen = Kwf_Component_Generator_Abstract::getInstance($event->class, 'detail');
        $datas = $gen->getChildData(null, array('id' => $event->itemId, 'subroot' => $event->subroot, 'ignoreVisible' => true));
        $directories = array();
        foreach ($datas as $data) {
            $directory = $data->parent;
            if ($directory->isVisible()) {
                if (!in_array($directory, $directories)) $directories[] = $directory;
            }
        }
        foreach ($directories as $directory) {
            $this->_fireTagEvent('ContentChanged', $directory);
            $this->_fireTagEvent('PartialsChanged', $directory);
            if ($this->_usesPartialId()) {
                $this->_fireTagEvent('PartialChanged', $directory, $event->itemId);
            } else {
                $this->_fireTagEvent('AllPartialChanged', $directory);
            }
        }
    }

    public function onDirectoryUpdate(Kwc_Directories_List_EventAbstract $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class, $event->subroot));
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_AllPartialChanged($this->_class, $event->subroot));
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_PartialsChanged($this->_class, $event->subroot));
    }
}
