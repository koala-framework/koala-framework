<?php
class Kwc_Legacy_Columns_Events extends Kwc_Abstract_List_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_ContentWidthChanged',
            'callback' => 'onContentWidthChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveContentWidthChanged',
            'callback' => 'onRecursiveContentWidthChanged'
        );
        return $ret;
    }

    public function onRowInsertOrDelete(Kwf_Events_Event_Row_Abstract $event)
    {
        parent::onRowInsertOrDelete($event);
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $event->row->component_id, array('limit'=>1, 'ignoreVisible'=>true)
        );
        if ($c && $c->componentClass == $this->_class) {
            if ($event->row->visible) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentWidthChanged(
                    $this->_class, $c
                ));
            }
        }
    }

    public function onRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        parent::onRowUpdate($event);
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $event->row->component_id, array('limit'=>1, 'ignoreVisible'=>true)
        );
        if ($c && $c->componentClass == $this->_class) {
            if ($event->isDirty('width') || $event->isDirty('visible')) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentWidthChanged(
                    $this->_class, $c
                ));
            }
        }
    }

    public function onContentWidthChanged(Kwf_Component_Event_Component_ContentWidthChanged $event)
    {
        $c = $event->component;
        $this->fireEvent(new Kwf_Component_Event_ComponentClassPage_ContentChanged(
            $this->_class, $c->getPageOrRoot()
        ));
    }

    public function onRecursiveContentWidthChanged(Kwf_Component_Event_Component_RecursiveContentWidthChanged $event)
    {
        $c = $event->component;
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $c
        ));
    }
}
