<?php
class Vpc_Advanced_SocialBookmarks_Events extends Vps_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ownModel = Vpc_Abstract::getSetting($this->_class, 'ownModel');
        $models = Vps_Model_Abstract::getInstance($ownModel)
            ->getDependentModels();
        $model = $models['Networks'];
        $ret[] = array(
            'class' => $model,
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onOwnRowUpdate'
        );
        $ret[] = array(
            'class' => $models['Networks'],
            'event' => 'Vps_Component_Event_Row_Inserted',
            'callback' => 'onOwnRowUpdate'
        );
        $ret[] = array(
            'class' => $models['Networks'],
            'event' => 'Vps_Component_Event_Row_Deleted',
            'callback' => 'onOwnRowUpdate'
        );
        return $ret;
    }

    public function onOwnRowUpdate(Vps_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
