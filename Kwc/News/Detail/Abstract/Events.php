<?php
class Kwc_News_Detail_Abstract_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $generators['child']['component']['content'],
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
        );
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_RowUpdated',
            'callback' => 'onDirectoryRowUpdated'
        );
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, $event->getParentDbId()
        ));
    }

    public function onDirectoryRowUpdated(Kwf_Component_Event_Component_RowUpdated $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->dbId
        ));
    }
}
