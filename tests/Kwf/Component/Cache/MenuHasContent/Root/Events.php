<?php
class Kwf_Component_Cache_MenuHasContent_Root_Events extends Kwc_Root_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onHasContentChanged',
        );
        return $ret;
    }

    public static $hasContentChanged = array();

    public function onHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $ev)
    {
        self::$hasContentChanged[] = $ev->dbId;
    }
}
