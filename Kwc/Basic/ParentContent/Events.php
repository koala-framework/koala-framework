<?php
class Kwc_Basic_ParentContent_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageParentChanged'
        );
        return $ret;
    }

    public function onPageParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId) as $data) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $this->_class, $data->componentId
            ));
        }
    }
}
