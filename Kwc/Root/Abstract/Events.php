<?php
class Kwc_Root_Abstract_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwf_Component_Generator_Abstract::getInstances($this->_class) as $g) {
            if ($g->getGeneratorFlag('box')) {
                foreach ($g->getChildComponentClasses() as $c) {
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
                }
            }
        }
        
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
        }
    }

    public function onUniqueBoxHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $pageId = $event->dbId;
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($pageId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveMasterContentChanged(
                $this->_class, $c->getPageOrRoot()->componentId
            ));
        }
    }

    public function onBoxRecursiveHasContentChanged(Kwf_Component_Event_Component_RecursiveHasContentChanged $event)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId);
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveMasterContentChanged(
            $this->_class, $c->getPageOrRoot()->componentId
        ));
    }
}
