<?php
class Kwc_Box_SwitchLanguage_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onPageAddedRemoved'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onPageAddedRemoved'
        );
        return $ret;
    }

    public function onPageAddedRemoved(Kwf_Component_Event_Component_Abstract $ev)
    {
        if (is_instance_of($ev->component->componentClass, 'Kwc_Chained_Start_Component')) { // Whole language has been disabled
            $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class));
        } else {
            $master = $ev->component;
            if (is_instance_of($master->componentClass, 'Kwc_Chained_Trl_Component')) {
                $master = $master->chained;
            }
            foreach ($master->getRecursiveChildComponents(array('componentClass' => $this->_class)) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $c
                ));
            }
        }
    }
}
