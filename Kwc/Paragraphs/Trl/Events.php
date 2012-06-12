<?php
class Kwc_Paragraphs_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($masterComponentClass, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onMasterRowUpdate'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($masterComponentClass, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onMasterRowDelete'
        );
        return $ret;
    }

    protected function onRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        if ($event->isDirty('visible')) {
            $id = $event->row->component_id;
            $id = substr($id, 0, strrpos($id, '-'));
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($id) as $c) {
                $this->fireEvent(
                    new Kwf_Component_Event_Component_ContentChanged($this->_class, $c)
                );
                $this->fireEvent(
                    new Kwf_Component_Event_Component_HasContentChanged($this->_class, $c)
                );
            }
        }
    }

    protected function onMasterRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        if ($event->isDirty('pos')) {

            $chainedType = 'Trl';

            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->row->component_id) as $c) {
                $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($c, $chainedType);
                foreach ($chained as $c) {
                    $this->fireEvent(
                        new Kwf_Component_Event_Component_ContentChanged($this->_class, $c)
                    );
                }
            }
        }
    }

    protected function onMasterRowDelete(Kwf_Component_Event_Row_Abstract $event)
    {
        $chainedType = 'Trl';

        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->row->component_id) as $c) {
            $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($c, $chainedType);
            foreach ($chained as $c) {
                $this->fireEvent(
                    new Kwf_Component_Event_Component_ContentChanged($this->_class, $c)
                );
                $this->fireEvent(
                    new Kwf_Component_Event_Component_HasContentChanged($this->_class, $c)
                );
            }
        }
    }
}
