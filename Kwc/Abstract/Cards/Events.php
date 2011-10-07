<?php
class Kwc_Abstract_Cards_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        foreach ($generators['child']['component'] as $component) {
            $ret[] = array(
                'class' => $component,
                'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                'callback' => 'onChildHasContentChange'
            );
        }
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, str_replace('-child', '', $event->dbId)
        ));
    }

    // call contentChanged for child when changing child-component
    public function onOwnRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        parent::onOwnRowUpdate($event);
        if ($event->isDirty('component')) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $event->row->component_id . '-child'
            ));
        }
    }
}
