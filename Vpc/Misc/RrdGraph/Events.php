<?php
class Vpc_Misc_RrdGraph_Events extends Vpc_Abstract_Events
{
    public function onOwnRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        parent::onOwnRowUpdate($event);
        $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId(
            $event->row->component_id, array('componentClass' => $this->_class)
        );
        foreach ($components as $component) {
            $this->fireEvent(new Vps_Component_Event_Media_Changed(
                $this->_class, $event->row->component_id
            ));
        }
    }
}
