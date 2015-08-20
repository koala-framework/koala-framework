<?php
class Kwc_Box_SwitchLanguage_AlternativeLanguageLinks_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach ($this->_getCreatingClasses($this->_class) as $class) {
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_Component_ContentChanged',
                'callback' => 'onLanguageChanged'
            );
        }
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onRecursiveUrlChanged'
        );
        return $ret;
    }

    public function onLanguageChanged(Kwf_Component_Event_Component_ContentChanged $ev)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $ev->component->getChildComponent('-alternativeLanguageLinks')
        ));
    }

    public function onUrlChanged(Kwf_Component_Event_Page_UrlChanged $ev)
    {
        $this->_fireUrlChanged($ev->component, 'Kwf_Component_Event_Component_ContentChanged');
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $ev)
    {
        $this->_fireUrlChanged($ev->component, 'Kwf_Component_Event_Component_RecursiveContentChanged');
    }

    private function _fireUrlChanged($component, $eventClass)
    {
        $master = $component;
        if (is_instance_of($master->componentClass, 'Kwc_Chained_Trl_Component')) {
            $master = $master->chained;
        }
        $components = Kwc_Chained_Abstract_Component::getAllChainedByMaster($master, 'Trl');
        $components[] = $master;
        foreach ($components as $c) {
            $s = array('componentClass' => $this->_class);
            foreach ($c->getRecursiveChildComponents($s) as $c) {
                $this->fireEvent(new $eventClass($this->_class, $c));
            }
        }
    }
}
