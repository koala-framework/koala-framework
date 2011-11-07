<?php
class Kwc_Directories_Item_Detail_Events extends Kwc_Abstract_Composite_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_RowUpdated',
            'callback' => 'onGeneratorRowUpdate'
        );
        return $ret;
    }

    public function onGeneratorRowUpdate(Kwf_Component_Event_Component_RowUpdated $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($event->class, $event->dbId));
    }
}
