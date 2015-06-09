<?php
class Kwc_Advanced_DownloadsTree_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowInsertOrDelete(Kwf_Events_Event_Row_Abstract $event)
    {
        $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            $event->row->getParentRow('Project')->component_id
        );
        foreach ($cmps as $c) {
            if ($c->componentClass == $this->_class) {
                $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                    $this->_class, $c
                ));
            }
        }
    }

    public function onRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        if ($event->isDirty('visible')) {

            $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
                $event->row->getParentRow('Project')->component_id
            );
            foreach ($cmps as $c) {
                if ($c->componentClass == $this->_class) {
                    $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                        $this->_class, $c
                    ));
                }
            }
        }
    }
}
