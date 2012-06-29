<?php
class Kwc_Chained_Trl_GeneratorEvents_Static_Page extends Kwc_Chained_Trl_GeneratorEvents_Static_PseudoPage
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
