<?php
class Kwc_Abstract_List_Trl_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $cls = strpos($this->_class, '.') ? substr($this->_class, 0, strpos($this->_class, '.')) : $this->_class;
        $m = call_user_func(array($cls, 'createChildModel'), $this->_class);
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $cls = strpos($masterComponentClass, '.') ? substr($masterComponentClass, 0, strpos($masterComponentClass, '.')) : $masterComponentClass;
        $m = call_user_func(array($cls, 'createChildModel'), $masterComponentClass);
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onMasterRowUpdate'
        );
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onMasterRowDelete'
        );
        return $ret;
    }

    public function onRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        if ($event->isDirty('visible')) {
            //component_id is the child component id, not as in master the list component id
            $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
                $event->row->component_id, array('ignoreVisible'=>true)
            );
            foreach ($cmps as $c) {
                $c = $c->parent;
                if ($c->componentClass == $this->_class) {
                    $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                        $this->_class, $c
                    ));
                    $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                        $this->_class, $c
                    ));
                }
            }
        }
    }

    protected function onMasterRowUpdate(Kwf_Events_Event_Row_Abstract $event)
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

    protected function onMasterRowDelete(Kwf_Events_Event_Row_Abstract $event)
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
