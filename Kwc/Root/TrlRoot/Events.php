<?php
class Kwc_Root_TrlRoot_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $model = Kwc_Abstract::createChildModel($this->_class);
        $siblingModels = $model->getSiblingModels();
        $ret[] = array(
            'class' => get_class($siblingModels[0]),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onLanguageUpdate'
        );
        return $ret;
    }

    public function onLanguageUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('visible')) {
            $c = Kwf_Component_Data_Root::getInstance()->getChildComponent(
                array('id' => '-' . $event->row->id, 'ignoreVisible' => true)
            );
            if ($c) {
                if ($event->row->visible) {
                    $this->fireEvent(new Kwf_Component_Event_Page_Added(
                        $this->_class, $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED
                    ));
                } else {
                    $this->fireEvent(new Kwf_Component_Event_Page_Removed(
                        $this->_class, $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED
                    ));
                }
            }
        }
    }
}
