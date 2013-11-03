<?php
class Kwc_Box_Title_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageNameChanged'
        );
        return $ret;
    }

    public function onPageNameChanged(Kwf_Component_Event_Page_NameChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $event->component
        ));
    }
}
