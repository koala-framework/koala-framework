<?php
class Kwf_Component_Generator_Events_Static extends Kwf_Component_Generator_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentAdded'
        );
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentRemoved'
        );
        return $ret;
    }

    //overridden in Page_Events_Static
    protected function _fireComponentEvent($event, $dbId, $flag)
    {
        $cls = 'Kwf_Component_Event_Component_'.$event;
        $g = $this->_getGenerator();
        foreach ($g->getChildComponentClasses() as $k=>$c) {
            $this->fireEvent(new $cls($c, $dbId.$g->getIdSeparator().$k, $flag));
        }
    }

    public function onComponentAdded(Kwf_Component_Event_Component_Added $ev)
    {
        $this->_fireComponentEvent('Added', $ev->dbId, $ev->flag);
    }

    public function onComponentRemoved(Kwf_Component_Event_Component_Removed $ev)
    {
        $this->_fireComponentEvent('Removed', $ev->dbId, $ev->flag);
    }
}
