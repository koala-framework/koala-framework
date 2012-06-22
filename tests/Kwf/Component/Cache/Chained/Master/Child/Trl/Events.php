<?php
class Kwf_Component_Cache_Chained_Master_Child_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $ret[] = array(
            'class' => $masterComponentClass,
            'event' => 'Kwf_Component_Event_Component_RowUpdated',
            'callback' => 'onGeneratorRowUpdate'
        );
        return $ret;
    }

    public function onGeneratorRowUpdate(Kwf_Component_Event_Component_RowUpdated $event)
    {
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl', array('ignoreVisible'=>true));
        foreach ($chained as $i) {
            if ($i->componentClass != $this->_class) {
                throw new Kwf_Exception('getAllChainedByMaster returned incorrect component');
            }

            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $i));
        }
    }
}
