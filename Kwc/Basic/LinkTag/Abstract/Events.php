<?php
class Kwc_Basic_LinkTag_Abstract_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach ($this->_getCreatingClasses($this->_class) as $c) {
            if (is_instance_of($c, 'Kwc_Basic_LinkTag_Component')) {
                $ret[] = array(
                    'class' => $c,
                    'event' => 'Kwf_Component_Event_Component_ContentChanged',
                    'callback' => 'onLinkTagContentChanged'
                );
            }
        }
        return $ret;
    }

    public function onLinkTagContentChanged(Kwf_Component_Event_Component_ContentChanged $ev)
    {
        //title text could have changed
        $c = $ev->component->getChildComponent('-child');
        if ($c && $c->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
        }
    }
}
