<?php
class Kwc_Box_Assets_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (Kwc_Abstract::getFlag($c, 'assetsPackage')) {
                $ret[] = array(
                    'class' => $c,
                    'event' => 'Kwf_Component_Event_Component_Added',
                    'callback' => 'onComponentAddedRemoved'
                );
                $ret[] = array(
                    'class' => $c,
                    'event' => 'Kwf_Component_Event_Component_Removed',
                    'callback' => 'onComponentAddedRemoved'
                );
            }
        }
        return $ret;
    }

    public function onComponentAddedRemoved(Kwf_Component_Event_Component_Abstract $ev)
    {
        $page = $ev->component->getPage();
        if ($page) {
            foreach ($page->getChildComponents(array('componentClass'=>$this->_class)) as $assetsBox) {
                $this->fireEvent(
                    new Kwf_Component_Event_Component_ContentChanged(
                        $this->_class, $assetsBox
                    )
                );
            }
        }
    }
}
