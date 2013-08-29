<?php
class Kwc_Statistics_OptBox_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (is_instance_of($class, 'Kwc_Statistics_Opt_Component')) {
                $ret[] = array(
                    'class' => $class,
                    'event' => 'Kwf_Component_Event_Component_Added',
                    'callback' => 'onOptComponentChanged'
                );
                $ret[] = array(
                    'class' => $class,
                    'event' => 'Kwf_Component_Event_Component_Removed',
                    'callback' => 'onOptComponentChanged'
                );
            }
        }
        return $ret;
    }

    public function onOptComponentChanged(Kwf_Component_Event_Component_Abstract $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass($this->_class, array('subroot' => $event->component));
        foreach ($components as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
        }
    }
}
