<?php
class Kwc_Newsletter_DeleteUnsubscribedJob extends Kwf_Util_Maintenance_Job_Abstract
{
    public function getFrequency()
    {
        return self::FREQUENCY_DAILY;
    }

    public function execute($debug)
    {
        $hashes = $this->_getHashes();

        foreach ($this->_getSubscribers() as $row) {
            $hash = md5($row->email);

            if (!in_array($hash, $hashes)) {
                Kwf_Model_Abstract::getInstance('Kwc_Newsletter_UnsubscribedEmailHashesModel')->createRow(array(
                    'id' => $hash
                ))->save();
                $hashes[] = $hash;
            }

            $row->delete();
        }
    }

    private function _getHashes()
    {
        $ret = array();

        foreach (Kwf_Model_Abstract::getInstance('Kwc_Newsletter_UnsubscribedEmailHashesModel')->export(
            Kwf_Model_Abstract::FORMAT_ARRAY, array()
        ) as $row) {
            $ret[] = $row['id'];
        }

        return $ret;
    }

    private function _getSubscribers()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('state', 'unsubscribed');
        $s->where(new Kwf_Model_Select_Expr_LowerEqual('date', new Kwf_Date(strtotime('-1 year'))));

        $select = new Kwf_Model_Select();
        $select->whereEquals('unsubscribed', true);
        $select->where(new Kwf_Model_Select_Expr_Child_Contains('Logs', $s));
        return Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Subscribe_Model')->getRows($select);
    }
}
