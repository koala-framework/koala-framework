<?php
class Kwc_Advanced_SocialBookmarks_Events extends Kwf_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ownModel = Kwc_Abstract::getSetting($this->_class, 'ownModel');
        $models = Kwf_Model_Abstract::getInstance($ownModel)
            ->getDependentModels();
        $model = $models['Networks'];
        $ret[] = array(
            'class' => $model,
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onNetworksRowUpdate'
        );
        $ret[] = array(
            'class' => $models['Networks'],
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onNetworksRowUpdate'
        );
        $ret[] = array(
            'class' => $models['Networks'],
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onNetworksRowUpdate'
        );
        return $ret;
    }

    public function onNetworksRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
