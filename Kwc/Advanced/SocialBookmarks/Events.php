<?php
class Kwc_Advanced_SocialBookmarks_Events extends Kwf_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ownModel = Kwc_Abstract::createOwnModel($this->_class);
        $models = Kwf_Model_Abstract::getInstance($ownModel)
            ->getDependentModels();
        $model = $models['Networks'];
        $ret[] = array(
            'class' => $model,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onNetworksRowUpdate'
        );
        $ret[] = array(
            'class' => $model,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onNetworksRowUpdate'
        );
        $ret[] = array(
            'class' => $model,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onNetworksRowUpdate'
        );
        return $ret;
    }

    public function onNetworksRowUpdate(Kwf_Events_Event_Row_Abstract $event)
    {
        $dbId = $event->row->component_id;
        $alternativeCmps = call_user_func(array($this->_class, 'getAlternativeComponents'));
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $alternativeCmps['inherit'], $c
            ));
        }
    }
}
