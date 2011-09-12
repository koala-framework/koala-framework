<?php
class Vpc_Basic_LinkTag_Event_Data extends Vpc_Basic_LinkTag_News_Data
{
    protected function _getData()
    {
        $m = Vpc_Abstract::createModel($this->componentClass);
        $eventId = $m->fetchColumnByPrimaryId('event_id', $this->dbId);

        if ($eventId) {
            return Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId('events_'.$eventId, array('subroot' => $this));
        }
        return false;
    }
}
