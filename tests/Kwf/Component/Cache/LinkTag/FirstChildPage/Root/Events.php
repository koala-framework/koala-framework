<?php
class Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Basic_LinkTag_FirstChildPage_Component',
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onHasContentChanged'
        );
        return $ret;
    }

    public function onHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, 'root'));
    }
}
