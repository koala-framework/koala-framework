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
            'event' => 'Kwc_Directories_List_EventRowUpdated',
            'callback' => 'onMasterChildRowUpdate'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventRowInserted',
            'callback' => 'onMasterChildRowInsert'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventRowDeleted',
            'callback' => 'onMasterChildRowDelete'
        );
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwc_Directories_List_EventModelUpdated',
            'callback' => 'onMasterChildModelUpdated'
        );

        // trl model (optional)
        $generator = Kwf_Component_Generator_Abstract::getInstance($this->_class, 'detail');
        if ($generator->getModel()) {
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Component_Event_Row_Updated',
                'callback' => 'onChildRowUpdate'
            );
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Component_Event_Row_Inserted',
                'callback' => 'onChildRowInsert'
            );
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Component_Event_Row_Deleted',
                'callback' => 'onChildRowDelete'
            );
            $ret[] = array(
                'class' => get_class($generator->getModel()),
                'event' => 'Kwf_Component_Event_Model_Updated',
                'callback' => 'onChildModelUpdated'
            );
        }
        return $ret;
    }

    // master model
    public function onMasterChildRowUpdate(Kwc_Directories_List_EventRowUpdated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventRowUpdated($this->_class, $event->itemId));
    }

    public function onMasterChildRowInsert(Kwc_Directories_List_EventRowInserted $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventRowInserted($this->_class, $event->itemId));
    }

    public function onMasterChildRowDelete(Kwc_Directories_List_EventRowDeleted $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventRowDeleted($this->_class, $event->itemId));
    }

    public function onMasterChildModelUpdated(Kwc_Directories_List_EventModelUpdated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventModelUpdated($this->_class));
    }

    // trl model (optional)
    public function onChildRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $dbId = $event->row->component_id;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('limit'=>1, 'ignoreVisible'=>true));
        if ($c) {
            $this->fireEvent(new Kwc_Directories_List_EventRowUpdated($this->_class, $c->id));
        }
    }

    public function onChildRowInsert(Kwf_Component_Event_Row_Inserted $event)
    {
        $dbId = $event->row->component_id;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('limit'=>1, 'ignoreVisible'=>true));
        if ($c) {
            $this->fireEvent(new Kwc_Directories_List_EventRowInserted($this->_class, $c->id));
        }
    }

    public function onChildRowDelete(Kwf_Component_Event_Row_Deleted $event)
    {
        $dbId = $event->row->component_id;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('limit'=>1, 'ignoreVisible'=>true));
        if ($c) {
            $this->fireEvent(new Kwc_Directories_List_EventRowDeleted($this->_class, $c->id));
        }
    }

    public function onChildModelUpdated(Kwf_Component_Event_Model_Updated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventModelUpdated($this->_class));
    }
}
