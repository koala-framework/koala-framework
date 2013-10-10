<?php
class Kwc_Newsletter_Detail_IsActiveData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        if ($row instanceof Kwc_Mail_Recipient_UnsubscribableInterface) {
            if ($row->getMailUnsubscribe() && $row->activated) {
                return trlKwf('unsubscribed');
            } else if (!$row->activated) {
                return trlKwf('not activated');
            } else if (!$row->getMailUnsubscribe() && $row->activated) {
                return trlKwf('active');
            }
        }
    }
}
