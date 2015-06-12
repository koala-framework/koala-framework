<?php
class Kwf_Component_Cache_CacheTag_Test_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwf_Component_Cache_CacheTag_Test_Model',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowUpdate(Kwf_Events_Event_Row_Updated $ev)
    {
        $this->fireEvent(
            new Kwf_Component_Event_ComponentClass_Tag_ContentChanged($this->_class, 'asdf')
        );
    }
}
