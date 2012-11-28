<?php
class Kwc_Composite_TwoColumns_Events extends Kwc_Abstract_Composite_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_ContentWidthChanged',
            'callback' => 'onContentWidthChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveContentWidthChanged',
            'callback' => 'onRecursiveContentWidthChanged'
        );
        return $ret;
    }

    public function onContentWidthChanged(Kwf_Component_Event_Component_ContentWidthChanged $event)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_ComponentClassPage_ContentChanged(
                $this->_class, $c->getPageOrRoot()->dbId
            ));
        }
    }

    public function onRecursiveContentWidthChanged(Kwf_Component_Event_Component_RecursiveContentWidthChanged $event)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId);
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $c->componentId
        ));
    }
}
