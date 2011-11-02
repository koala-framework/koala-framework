<?php
class Kwc_Box_InheritContent_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => Kwc_Abstract::getChildComponentClass($this->_class, 'child'),
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
        );
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        if (substr($event->dbId, -6) == '-child') {
            $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByDbId($event->dbId);
            foreach ($components as $component) {
                $ic = $component->parent;
                if ($ic->componentClass == $this->_class) {
                    $this->fireEvent(
                        new Kwf_Component_Event_Component_RecursiveContentChanged(
                            $ic->componentClass, $ic->componentId
                        )
                    );
                }
            }
        }
    }
}
