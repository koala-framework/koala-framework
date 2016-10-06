<?php
class Kwc_Basic_LinkTag_Event_Data extends Kwc_Basic_LinkTag_News_Data
{
    protected function _getData($select = array())
    {
        $m = Kwc_Abstract::createModel($this->componentClass);
        $eventId = $m->fetchColumnByPrimaryId('event_id', $this->dbId);

        if ($eventId) {
            return Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId('events_'.$eventId, array('subroot' => $this));
        }
        return false;
    }
}
