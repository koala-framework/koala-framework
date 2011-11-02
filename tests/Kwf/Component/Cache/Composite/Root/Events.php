<?php
class Kwf_Component_Cache_Composite_Root_Events extends Kwc_Abstract_Composite_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwf_Component_Cache_Composite_Root_Component',
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onHasContentChange'
        );
        return $ret;
    }

    public function onHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->dbId
        ));
    }
}
