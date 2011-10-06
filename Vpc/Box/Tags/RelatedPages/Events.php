<?php
class Vpc_Box_Tags_RelatedPages_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel',
            'event' => 'Vps_Component_Event_Row_Inserted',
            'callback' => 'onRowOperation'
        );
        $ret[] = array(
            'class' => 'Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel',
            'event' => 'Vps_Component_Event_Row_Deleted',
            'callback' => 'onRowOperation'
        );
        $ret[] = array(
            'class' => 'Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel',
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowOperation'
        );
        return $ret;
    }

    public function onRowOperation(Vps_Component_Event_Row_Abstract $event)
    {
        $model = $event->row->getModel();
        $select = $model->select()->whereEquals('tag_id', $event->row->tag_id);
        foreach ($model->getRows($select) as $row) {
            $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
                $this->_class, $row->component_id
            ));
            $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
                $this->_class, $row->component_id
            ));
        }
        /* todo events: dirty TagId
        $select = $model->select()->whereEquals('tag_id', $dirtyTagId);
        foreach ($model->getRows($select) as $row) {
            $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
                $this->_class, $row->component_id
            ));
            $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
                $this->_class, $row->component_id
            ));
        }
        */
    }
}
