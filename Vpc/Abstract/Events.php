<?php
class Vpc_Abstract_Events extends Vps_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        if (Vpc_Abstract::hasSetting($this->_class, 'ownModel')) {
            $ret[] = array(
                'class' => Vpc_Abstract::getSetting($this->_class, 'ownModel'),
                'event' => Vps_Component_Events::EVENT_ROW_UPDATE,
                'callback' => 'onOwnRowUpdate'
            );
        }
        return $ret;
    }

    public function onOwnRowUpdate($event, $row)
    {
        foreach ($this->_getComponentsByDbIdOwnClass($row->component_id) as $c) {
            self::fireEvent(Vps_Component_Events::EVENT_COMPONENT_CONTENT_CHANGE, $c->componentClass, $c->componentId);
            self::fireEvent(Vps_Component_Events::EVENT_COMPONENT_HAS_CONTENT_CHANGE, $c->componentClass, $c->componentId);
            //$hc = $c->getComponent()->hasContent(); //todo: make this more selective
            //if ($hc != getFromCache($c->componentId)) {
                //self::fireEvent(Vps_Component_Events::EVENT_COMPONENT_HAS_CONTENT_CHANGE, $c->componentClass, array('componentId'=>$c->componentId, 'hasContent'=>$hc));
            //}
        }
    }
}
