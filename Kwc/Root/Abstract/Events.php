<?php
class Kwc_Root_Abstract_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $componentClass) {
            $generators = Kwf_Component_Generator_Abstract::getInstances($componentClass, array('box'=>true));
            foreach ($generators as $g) {
                foreach ($g->getChildComponentClasses() as $c) {
                    //TODO: only listen to boxes that use if (hasContent(..)) in master template
                    //      (once this is implemented (Recursive)MasterContentChanged must be fired on (Recursive)ContentWidthChanged)
                    if ($g->hasSetting('unique') && $g->getSetting('unique')) {
                        $ret[] = array(
                            'class' => $c,
                            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                            'callback' => 'onUniqueBoxHasContentChanged'
                        );
                    } else {
                        $ret[] = array(
                            'class' => $c,
                            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                            'callback' => 'onBoxHasContentChanged'
                        );
                    }
                    $ret[] = array(
                        'class' => $c,
                        'event' => 'Kwf_Component_Event_Component_RecursiveHasContentChanged',
                        'callback' => 'onBoxRecursiveHasContentChanged'
                    );
                    $ret[] = array(
                        'class' => $c,
                        'event' => 'Kwf_Component_Event_ComponentClass_HasContentChanged',
                        'callback' => 'onBoxClassHasContentChanged'
                    );
                }
            }
        }

        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
            'callback' => 'onRecursiveAdded'
        );
        return $ret;
    }

    public function onBoxHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $pageId = $event->dbId;
        //TODO: it should be possible to find the page using only string opertions which would be faster
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($pageId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_MasterContentChanged(
                $this->_class, $c->getPageOrRoot()->dbId
            ));

            $boxSubtract = Kwc_Abstract::getSetting($this->_class, 'contentWidthBoxSubtract');
            //TODO hier sollte eigentlich der boxname verwendet werden, der muss nicht die id sein
            if (isset($boxSubtract[$c->id])) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentWidthChanged(
                    $this->_class, $c->getPageOrRoot()->dbId
                ));
            }
        }
    }

    public function onUniqueBoxHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $boxId = $event->dbId;
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($boxId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveMasterContentChanged(
                $this->_class, $c->getPageOrRoot()->componentId
            ));

            $boxSubtract = Kwc_Abstract::getSetting($this->_class, 'contentWidthBoxSubtract');
            //TODO hier sollte eigentlich der boxname verwendet werden, der muss nicht die id sein
            if (isset($boxSubtract[$c->id])) {
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentWidthChanged(
                    $this->_class, $c->getPageOrRoot()->componentId
                ));
            }
        }
    }

    public function onBoxRecursiveHasContentChanged(Kwf_Component_Event_Component_RecursiveHasContentChanged $event)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId);
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveMasterContentChanged(
            $this->_class, $c->getPageOrRoot()->componentId
        ));

        $boxSubtract = Kwc_Abstract::getSetting($this->_class, 'contentWidthBoxSubtract');
        //TODO hier sollte eigentlich der boxname verwendet werden, der muss nicht die id sein
        if (isset($boxSubtract[$c->id])) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentWidthChanged(
                $this->_class, $c->getPageOrRoot()->componentId
            ));
        }
    }

    public function onBoxClassHasContentChanged(Kwf_Component_Event_ComponentClass_HasContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_MasterContentChanged(
            $this->_class
        ));
    }

    public function onRecursiveAdded(Kwf_Component_Event_Component_RecursiveAdded $event)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId);
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveMasterContentChanged(
            $this->_class, $c->getPageOrRoot()->componentId
        ));
    }
}
