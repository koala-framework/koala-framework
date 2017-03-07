<?php
class Kwc_Newsletter_Subscribe_Update_20170307InitSubscriberLog extends Kwf_Update
{
    public function postUpdate()
    {
        $ret = parent::postUpdate();

        $rows = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Subscribe_Model')->export(
            Kwf_Model_Abstract::FORMAT_ARRAY, array()
        );
        if (count($rows) > 1000) echo "Add initial subscriber log, this can take some time...\n";

        $data = array();
        foreach ($rows as $row) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentById($row['newsletter_component_id'], array('ignoreVisible' => true));

            if ($row['unsubscribed'] == true) {
                $date = new Kwf_DateTime(date('Y-m-d H:i:s'));
                $logMessage = $c->trlKwf('Unsubscribed');
            } else if ($row['activated'] == false) {
                $date = new Kwf_DateTime(($row['subscribe_date'] != '0000-00-00 00:00:00') ? $row['subscribe_date'] : date('Y-m-d H:i:s'));
                $logMessage = $c->trlKwf('Subscribed');
            } else {
                $date = new Kwf_DateTime(($row['subscribe_date'] != '0000-00-00 00:00:00') ? $row['subscribe_date'] : date('Y-m-d H:i:s'));
                $logMessage = $c->trlKwf('Subscribed and activated');
            }

            $data[] = array(
                'subscriber_id' => $row['id'],
                'date' => $date->format('Y-m-d H:i:s'),
                'ip' => null,
                'message' => $logMessage,
                'source' => $c->trlKwf('Initial')
            );
        }

        Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Subscribe_LogsModel')->import(Kwf_Model_Abstract::FORMAT_ARRAY, $data);

        $db = Kwf_Registry::get('db');
        $db->query('ALTER TABLE `kwc_newsletter_subscribers` DROP `subscribe_date`');

        return $ret;
    }
}

