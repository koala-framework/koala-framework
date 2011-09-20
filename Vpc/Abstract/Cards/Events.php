<?php
class Vpc_Abstract_Cards_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        foreach ($generators['child']['component'] as $component) {
            $ret[] = array(
                'class' => $component,
                'event' => 'Vps_Component_Event_Component_HasContentChanged',
                'callback' => 'onChildHasContentChange'
            );
        }
        return $ret;
    }

    public function onChildHasContentChange(Vps_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, str_replace('-child', '', $event->dbId)
        ));
    }

    // call contentChanged for child when changing child-component
    public function onOwnRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        parent::onOwnRowUpdate($event);
        if ($event->isDirty('component')) {
            $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
                $this->_class, $event->row->component_id . '-child'
            ));
        }
    }
}
