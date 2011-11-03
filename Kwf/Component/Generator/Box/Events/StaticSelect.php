<?php
class Kwf_Component_Generator_Box_Events_StaticSelect extends Kwf_Component_Generator_Events_Static
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onOwnRowUpdate'
        );
        return $ret;
    }

    public function onOwnRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('component')) {
            $id = $event->row->component_id;
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($id) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($this->_getClassFromRow($event->row, true), $c->componentId));
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded($this->_getClassFromRow($event->row, false), $c->componentId));
            }
        }
    }

}
