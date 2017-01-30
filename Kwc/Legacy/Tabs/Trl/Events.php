<?php
class Kwc_Legacy_Tabs_Trl_Events extends Kwc_Abstract_List_Trl_Events
{
    public function onRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        parent::onRowUpdate($event);
        if ($event->isDirty('title')) {
            //component_id is the child component id, not as in master the list component id
            $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
                $event->row->component_id, array('ignoreVisible'=>true)
            );
            foreach ($cmps as $c) {
                $c = $c->parent;
                if ($c->componentClass == $this->_class) {
                    $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                        $this->_class, $c
                    ));
                }
            }
        }
    }
}
