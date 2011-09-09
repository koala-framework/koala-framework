<?php
class Vpc_Basic_LinkTag_Event_Data extends Vpc_Basic_LinkTag_News_Data
{
    protected function _getData()
    {
        $m = Vpc_Abstract::createModel($this->componentClass);
        if ($m->getProxyModel() instanceof Vps_Model_Db) {
            //performance, avoid model overhead
            $sql = "SELECT event_id FROM ".$m->getProxyModel()->getTableName()." WHERE component_id=?";
            $eventId = Vps_Registry::get('db')->query($sql, $this->dbId)->fetchColumn();
        } else {
            $row = $m->getRow($this->dbId);
            $eventId = $row ? $row->event_id : false;
        }

        if ($eventId) {
            return Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId('events_'.$eventId, array('subroot' => $this));
        }
        return false;
    }
}
