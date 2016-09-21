<?php
class Kwc_Chained_Abstract_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onMasterContentChanged',
        );
        return $ret;
    }

    public function onMasterContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
    }
}
