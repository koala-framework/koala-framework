<?php
class Kwf_Component_Generator_Page_Events_Table extends Kwf_Component_Generator_PseudoPage_Events_Table
{
    protected function _fireComponentEvent($eventType, Kwf_Component_Data $c, $flag)
    {
        parent::_fireComponentEvent($eventType, $c, $flag);
        $cls = 'Kwf_Component_Event_Page_'.$eventType;
        $this->fireEvent(new $cls($c->componentClass, $c, $flag));
    }
}
