<?php
class Kwc_Box_Tags_RelatedPages_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwf_Component_Generator_Plugin_Tags_ComponentsToTagsModel',
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onRowOperation'
        );
        $ret[] = array(
            'class' => 'Kwf_Component_Generator_Plugin_Tags_ComponentsToTagsModel',
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onRowOperation'
        );
        $ret[] = array(
            'class' => 'Kwf_Component_Generator_Plugin_Tags_ComponentsToTagsModel',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowOperation'
        );
        return $ret;
    }

    public function onRowOperation(Kwf_Events_Event_Row_Abstract $event)
    {
        $model = $event->row->getModel();
        $select = $model->select()->whereEquals('tag_id', $event->row->tag_id);
        foreach ($model->getRows($select) as $row) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($row->component_id) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $c
                ));
                $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                    $this->_class, $c
                ));
            }
        }
        /* todo events: dirty TagId
        $select = $model->select()->whereEquals('tag_id', $dirtyTagId);
        foreach ($model->getRows($select) as $row) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($row->component_id) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $c
                ));
                $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                    $this->_class, $c
                ));
            }
        }
        */
    }
}
