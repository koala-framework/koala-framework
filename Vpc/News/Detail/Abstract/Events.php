<?php
class Vpc_News_Detail_Abstract_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $generators['child']['component']['content'],
            'event' => 'Vps_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
        );
        return $ret;
    }

    public function onChildHasContentChange(Vps_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
            $this->_class, $event->getParentComponentId($event->dbId)
        ));
    }
}
