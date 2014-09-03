<?php
class Kwc_Chained_Trl_Events extends Kwc_Chained_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        if (Kwc_Abstract::hasSetting($this->_class, 'throwContentChangedOnOwnMasterModelUpdate')
            || Kwc_Abstract::hasSetting($this->_class, 'throwHasContentChangedOnMasterRowColumnsUpdate')
        ) {
            $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
            $m = Kwc_Abstract::createOwnModel($masterComponentClass);
            if (!$m) {
                throw new Kwf_Exception("Master component '$masterComponentClass' doesn't have ownModel");
            }
            $ret[] = array(
                'class' => $m,
                'event' => 'Kwf_Events_Event_Row_Updated',
                'callback' => 'onMasterOwnRowUpdate'
            );
            $ret[] = array(
                'class' => $m,
                'event' => 'Kwf_Events_Event_Row_Inserted',
                'callback' => 'onMasterOwnRowUpdate'
            );
        }
        return $ret;
    }

    public function onMasterOwnRowUpdate(Kwf_Events_Event_Row_Abstract $event)
    {
        $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            $event->row->component_id, array('ignoreVisible'=>true)
        );
        foreach ($cmps as $c) {
            $chainedType = 'Trl';
            $select = array('ignoreVisible'=>true);
            $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($c, $chainedType, $select);
            foreach ($chained as $i) {
                if ($i->componentClass != $this->_class) { continue; }
                if (Kwc_Abstract::hasSetting($this->_class, 'throwContentChangedOnOwnMasterModelUpdate')) {
                    $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                        $this->_class, $i
                    ));
                }
                if (Kwc_Abstract::hasSetting($this->_class, 'throwHasContentChangedOnMasterRowColumnsUpdate')) {
                    $cols = Kwc_Abstract::getSetting($this->_class, 'throwHasContentChangedOnMasterRowColumnsUpdate');
                    if ($event->isDirty($cols)) {
                        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                            $this->_class, $i
                        ));
                    }
                }
            }
        }
    }
}
