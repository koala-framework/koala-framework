<?php
class Kwc_Chained_Trl_GeneratorEvents_Table_Page extends Kwc_Chained_Trl_GeneratorEvents_Table_PseudoPage
{
    protected function _fireComponentEvent($eventType, Kwf_Component_Data $c, $flag)
    {
        parent::_fireComponentEvent($eventType, $c, $flag);
        $cls = 'Kwf_Component_Event_Page_'.$eventType;
        $this->fireEvent(new $cls($c->componentClass, $c, $flag));
    }
}
