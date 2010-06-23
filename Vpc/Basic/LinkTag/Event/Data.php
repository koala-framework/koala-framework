<?php
class Vpc_Basic_LinkTag_Event_Data extends Vpc_Basic_LinkTag_News_Data
{
    protected function _getData()
    {
        if (($row = $this->_getRow()) && $row->event_id) {
            return Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId('events_'.$row->event_id, array('subroot' => $this));
        }
        return false;
    }
}
