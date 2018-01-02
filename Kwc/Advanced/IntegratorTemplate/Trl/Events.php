<?php
class Kwc_Advanced_IntegratorTemplate_Trl_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChanged'
        );
        return $ret;
    }

    public function onContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $event->component));
    }
}
