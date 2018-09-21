<?php
class Kwc_Columns_Abstract_Trl_Events extends Kwc_Abstract_Events
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
        $cls = strpos($masterComponentClass, '.') ? substr($masterComponentClass, 0, strpos($masterComponentClass, '.')) : $masterComponentClass;
        $childModel = call_user_func(array($cls, 'createChildModel'), $masterComponentClass);
        $ret[] = array(
            'class' => $childModel,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onMasterRowUpdate'
        );
        $ret[] = array(
            'class' => $childModel,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onMasterRowDelete'
        );
        $ownModel = call_user_func(array($cls, 'createOwnModel'), $masterComponentClass);
        $ret[] = array(
            'class' => $ownModel,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onMasterOwnRowUpdate'
        );
        $ret[] = array(
            'class' => $ownModel,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onMasterOwnRowUpdate'
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

    public function onMasterRowUpdate(Kwf_Events_Event_Row_Abstract $event)
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

    public function onMasterRowDelete(Kwf_Events_Event_Row_Abstract $event)
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
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $i
                ));
            }
        }
    }
}
