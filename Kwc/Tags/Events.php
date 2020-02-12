<?php
class Kwc_Tags_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Tags_ComponentToTag',
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onTagRowUpdate'
        );
        $ret[] = array(
            'class' => 'Kwc_Tags_ComponentToTag',
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onTagRowUpdate'
        );
        $ret[] = array(
            'class' => 'Kwc_Tags_Suggestions_Model',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onTagRowUpdate'
        );
        return $ret;
    }

    public function onTagRowUpdate(Kwf_Events_Event_Row_Abstract $ev)
    {
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($ev->row->component_id);
        foreach ($components as $component) {
            $this->fireEvent(
                new Kwf_Component_Event_Component_ContentChanged($this->_class, $component)
            );
        }
    }
}
