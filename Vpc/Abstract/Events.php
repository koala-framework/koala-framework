<?php
class Vpc_Abstract_Events extends Vps_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        if (Vpc_Abstract::hasSetting($this->_class, 'ownModel')) {
            $ret[] = array(
                'class' => Vpc_Abstract::getSetting($this->_class, 'ownModel'),
                'event' => 'Vps_Component_Event_Row_Updated',
                'callback' => 'onOwnRowUpdate'
            );
        }
        return $ret;
    }

    public function onOwnRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        /*
        foreach (Vps_Component_Data_Root::getInstance()->getComponentsByDbId($row->component_id, array('componentClass'=>$this->_class)) as $c) {
            self::fireEvent('Vps_Component_Event_Component_ContentChanged', $c->componentClass, $c->componentId);
            self::fireEvent('Vps_Component_Event_Component_HasContentChanged', $c->componentClass, $c->componentId);
            //$hc = $c->getComponent()->hasContent(); //todo: make this more selective
            //if ($hc != getFromCache($c->componentId)) {
                //self::fireEvent(Vps_Component_Events::EVENT_COMPONENT_HAS_CONTENT_CHANGE, $c->componentClass, array('componentId'=>$c->componentId, 'hasContent'=>$hc));
            //}
        }
        */
    }
}
