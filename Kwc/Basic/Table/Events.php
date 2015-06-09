<?php
class Kwc_Basic_Table_Events extends Kwc_Abstract_Composite_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $m = Kwc_Abstract::createChildModel($this->_class);
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onChildRowUpdate'
        );
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onChildRowUpdate'
        );
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onChildRowUpdate'
        );
        return $ret;
    }

    public function onChildRowUpdate(Kwf_Events_Event_Row_Abstract $event)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $event->row->component_id, array('limit'=>1, 'ignoreVisible'=>true)
        );
        if ($c && $c->componentClass == $this->_class && $c->isVisible()) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));
        }
    }
}
