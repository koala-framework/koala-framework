<?php
class Kwc_Abstract_Events extends Kwf_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        if (Kwc_Abstract::hasSetting($this->_class, 'ownModel')) {
            $ret[] = array(
                'class' => Kwc_Abstract::getSetting($this->_class, 'ownModel'),
                'event' => 'Kwf_Component_Event_Row_Updated',
                'callback' => 'onOwnRowUpdate'
            );
            $ret[] = array(
                'class' => Kwc_Abstract::getSetting($this->_class, 'ownModel'),
                'event' => 'Kwf_Component_Event_Row_Inserted',
                'callback' => 'onOwnRowUpdate'
            );
        }
        return $ret;
    }

    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
    }

    protected function _onOwnRowUpdateNotVisible(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
    }

    //override _onOwnRowUpdate to implement custom functionality
    public final function onOwnRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            $event->row->component_id, array('ignoreVisible'=>true)
        );
        foreach ($cmps as $c) {
            if ($c->componentClass == $this->_class) {
                if ($c->isVisible()) {
                    $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                        $this->_class, $c
                    ));
                    if (Kwc_Abstract::hasSetting($this->_class, 'throwHasContentChangedOnRowColumnsUpdate')) {
                        $columns = Kwc_Abstract::hasSetting($this->_class, 'throwHasContentChangedOnRowColumnsUpdate');
                        if ($event->isDirty(Kwc_Abstract::getSetting($this->_class, 'throwHasContentChangedOnRowColumnsUpdate'))) {
                            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                                $this->_class, $c
                            ));
                        }
                    }
                    $this->_onOwnRowUpdate($c, $event);
                    $this->_onOwnRowUpdateNotVisible($c, $event);
                } else {
                    $this->_onOwnRowUpdateNotVisible($c, $event);
                }
            }
        }
    }
}
