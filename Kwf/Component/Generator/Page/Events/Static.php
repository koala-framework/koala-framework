<?php
class Kwf_Component_Generator_Page_Events_Static extends Kwf_Component_Generator_Events_Static
{
    protected function _fireComponentEvent($event, $dbId, $flag)
    {
        parent::_fireComponentEvent($event, $dbId, $flag);
        $cls = 'Kwf_Component_Event_Page_'.$event;
        $g = $this->_getGenerator();
        foreach ($g->getChildComponentClasses() as $k=>$c) {
            $this->fireEvent(new $cls($c, $dbId.$g->getIdSeparator().$k, $flag));
        }

    }
}