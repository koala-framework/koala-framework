<?php
class Kwc_Box_TitleEditable_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageNameChanged'
        );
        return $ret;
    }

    public function onPageNameChanged(Kwf_Component_Event_Page_NameChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $event->component
        ));
    }

    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        if (Kwc_Abstract::getFlag($c->parent->componentClass, 'subroot') || $c->parent instanceof Kwf_Component_Data_Root) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $this->_class, $c
            ));
        }
    }
}
