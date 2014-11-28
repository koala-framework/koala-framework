<?php
class Kwc_Columns_Trl_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $generators['child']['component']['child'],
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
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

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $c = $event->component->parent;
        if ($c->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->component->parent
            ));
        }
    }

    protected function onMasterRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        if ($event->isDirty('pos') || $event->isDirty('visible')) {

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
