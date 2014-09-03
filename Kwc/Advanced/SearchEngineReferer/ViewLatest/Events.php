<?php
class Kwc_Advanced_SearchEngineReferer_ViewLatest_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (in_array('Kwc_Advanced_SearchEngineReferer_Component', Kwc_Abstract::getParentClasses($class))) {
                $m = Kwc_Abstract::createChildModel($class);
                $ret[] = array(
                    'class' => $m,
                    'event' => 'Kwf_Events_Event_Row_Inserted',
                    'callback' => 'onRowInsert'
                );
            }
        }
        return $ret;
    }

    public function onRowInsert(Kwf_Events_Event_Row_Inserted $event)
    {
        // Kwc_Advanced_SearchEngineReferer_Component::processInput saves not dbIb but componentId
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($event->row->component_id);
        if ($component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component->getChildComponent('-view')
            ));
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $component->getChildComponent('-view')
            ));
        }
    }
}
