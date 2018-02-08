<?php
class Kwc_Directories_Item_Directory_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();

        // master model
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemUpdated',
            'callback' => 'onMasterChildRowUpdate'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemInserted',
            'callback' => 'onMasterChildRowInsert'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemDeleted',
            'callback' => 'onMasterChildRowDelete'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventItemsUpdated',
            'callback' => 'onMasterChildModelUpdated'
        );

        // trl model (optional)
        $generator = Kwf_Component_Generator_Abstract::getInstance($this->_class, 'detail');
        if ($generator->getModel()) {
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Events_Event_Row_Updated',
                'callback' => 'onChildRowUpdate'
            );
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Events_Event_Row_Inserted',
                'callback' => 'onChildRowInsert'
            );
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Events_Event_Row_Deleted',
                'callback' => 'onChildRowDelete'
            );
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Events_Event_Model_Updated',
                'callback' => 'onChildModelUpdated'
            );
        }
        return $ret;
    }

    // master model
    public function onMasterChildRowUpdate(Kwc_Directories_List_EventItemUpdated $event)
    {
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->subroot, 'Trl', array()) as $sr) {
            $this->fireEvent(new Kwc_Directories_List_EventItemUpdated($this->_class, $event->itemId, $sr));
        }
    }

    public function onMasterChildRowInsert(Kwc_Directories_List_EventItemInserted $event)
    {
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->subroot, 'Trl', array()) as $sr) {
            $this->fireEvent(new Kwc_Directories_List_EventItemInserted($this->_class, $event->itemId, $sr));
        }
    }

    public function onMasterChildRowDelete(Kwc_Directories_List_EventItemDeleted $event)
    {
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->subroot, 'Trl', array()) as $sr) {
            $this->fireEvent(new Kwc_Directories_List_EventItemDeleted($this->_class, $event->itemId, $sr));
        }
    }

    public function onMasterChildModelUpdated(Kwc_Directories_List_EventItemsUpdated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventItemsUpdated($this->_class, $event->subroot));
    }

    // trl model (optional)
    public function onChildRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        $dbId = $event->row->component_id;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('limit'=>1, 'ignoreVisible'=>true));
        if ($c && $c->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwc_Directories_List_EventItemUpdated($this->_class, $c->id, $c->getSubroot()));
            $this->fireEvent(new Kwc_Directories_List_EventItemDeleted($this->_class, $c->id, $c->getSubroot()));
        }
    }

    public function onChildRowInsert(Kwf_Events_Event_Row_Inserted $event)
    {
        $dbId = $event->row->component_id;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('limit'=>1, 'ignoreVisible'=>true));
        if ($c && $c->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwc_Directories_List_EventItemInserted($this->_class, $c->id, $c->getSubroot()));
        }
    }

    public function onChildRowDelete(Kwf_Events_Event_Row_Deleted $event)
    {
        $dbId = $event->row->component_id;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('limit'=>1, 'ignoreVisible'=>true));
        if ($c && $c->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwc_Directories_List_EventItemDeleted($this->_class, $c->id, $c->getSubroot()));
        }
    }

    public function onChildModelUpdated(Kwf_Events_Event_Model_Updated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventItemsUpdated($this->_class, $event->subroot));
    }
}
