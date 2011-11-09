<?php
class Kwf_Component_Cache_Box_IcRoot_Events extends Kwf_Component_Abstract_Events
{
    public $countCalled = 0;

    protected function _init()
    {
        $this->countCalled = 0;
    }

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwf_Component_Cache_Box_IcRoot_InheritContent_Component',
            'event' => 'Kwf_Component_Event_Component_RecursiveHasContentChanged',
            'callback' => 'onHasContentChange'
        );
        return $ret;
    }

    public function onHasContentChange(Kwf_Component_Event_Component_RecursiveHasContentChanged $event)
    {
        $this->countCalled++;
    }
}
?>