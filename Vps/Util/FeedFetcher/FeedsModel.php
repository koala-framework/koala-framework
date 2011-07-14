<?php
class Vps_Util_FeedFetcher_FeedsModel extends Vps_Model_Db_Proxy
{
    protected $_rowClass = 'Vps_Util_FeedFetcher_FeedRow';
    protected $_table = 'feeds';

    protected function _selectToUpdate($minutes)
    {
        $s = $this->select();

        /* HUB CODE DEAKTIVIERT
        $s->where("
            (
                #von hub geupdated, alle 24h zur sicherheit manuell checken
                (last_update_fetch_new_entries < DATE_ADD(hub_last_update, interval -24*60*60 second))
                AND (
                    (last_update < DATE_ADD(NOW(), interval -24*60*60 second))
                    OR ISNULL(last_update)
                )
            )
            OR
            (
                #nicht von hub geupdated, alle \$minutes updaten
                (
                    (last_update_fetch_new_entries > DATE_ADD(hub_last_update, interval -24*60*60 second))
                    OR ISNULL(hub_last_update)
                )
                AND
                (
                    (last_update < DATE_ADD(NOW(), interval -$minutes*60 second))
                    OR ISNULL(last_update)
                )
            )
        ");
        */

        //das ist der zweite teil von dem oben
        $s->where("
            (last_update < DATE_ADD(NOW(), INTERVAL -$minutes MINUTE))
            OR ISNULL(last_update)
        ");

        $s->where("(TIMESTAMPDIFF(MINUTE,last_update_started,NOW()) > 15) OR ISNULL(last_update_started)");

        $s->order('last_update', 'ASC');
        return $s;
    }

    public function selectToUpdate()
    {
        $s = $this->_selectToUpdate(90 /*minutes*/);
        return $s;
    }

    public function getUpdateRows($limit, $debug = false)
    {
        $s = $this->selectToUpdate();
        $s->limit($limit);
        $ret = array();
        foreach ($this->getRows($s) as $r) {
            $ret[] = $r;
        }
        return $ret;
    }
}
