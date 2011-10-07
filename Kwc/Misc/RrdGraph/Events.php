<?php
class Kwc_Misc_RrdGraph_Events extends Kwc_Abstract_Events
{
    public function onOwnRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        parent::onOwnRowUpdate($event);
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            $event->row->component_id, array('componentClass' => $this->_class)
        );
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Media_Changed(
                $this->_class, $event->row->component_id
            ));
        }
    }
}
