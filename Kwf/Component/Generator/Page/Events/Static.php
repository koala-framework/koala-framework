<?php
class Kwf_Component_Generator_Page_Events_Static extends Kwf_Component_Generator_Events_Static
{
    protected function _fireComponentEvent($eventType, Kwf_Component_Event_Component_AbstractFlag $ev)
    {
        parent::_fireComponentEvent($eventType, $ev);
        $cls = 'Kwf_Component_Event_Page_'.$eventType;
        $g = $this->_getGenerator();
        foreach ($ev->component->getChildComponents(array('generator'=>$g->getGeneratorKey())) as $c) {
            $this->fireEvent(new $cls($c->componentClass, $c, $ev->flag));
        }
    }
}