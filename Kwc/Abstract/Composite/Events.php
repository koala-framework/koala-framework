<?php
class Kwc_Abstract_Composite_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        if (is_array($generators['child']['component'])) {
            foreach ($generators['child']['component'] as $component) {
                $ret[] = array(
                    'class' => $component,
                    'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                    'callback' => 'onChildHasContentChange'
                );
            }
        }
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, $event->getParentDbId()
        ));
    }
}
