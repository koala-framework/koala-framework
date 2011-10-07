<?php
class Vpc_Chained_Abstract_MasterAsChild_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $generators['child']['component'],
            'event' => 'Vps_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
        );
        return $ret;
    }

    public function onChildHasContentChange(Vps_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, str_replace('-child', '', $event->dbId)
        ));
    }
}
