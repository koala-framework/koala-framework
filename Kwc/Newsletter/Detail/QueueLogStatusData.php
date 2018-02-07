<?php
class Kwc_Newsletter_Detail_QueueLogStatusData extends Kwf_Data_Table
{
    public function load($row, array $info = array())
    {
        return self::getText(parent::load($row, $info));
    }

    public static function getText($status)
    {
        $ret = '-';

        if ($status === 'sent') {
            $ret = trlKwf('Sent');
        } else if ($status === 'failed') {
            $ret = trlKwf('Failed');
        } else if ($status === 'usernotfound') {
            $ret = trlKwf('Recipient not found');
        }

        return $ret;
    }
}
