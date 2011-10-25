<?php
class Kwc_Newsletter_Detail_IsActiveData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        if ($row->getMailUnsubscribe() && $row->activated) {
            return '<span class="unsubscribed">'.trlKwf('unsubscribed').'</span>';
        } else if (!$row->activated) {
            return '<span class="inactive">'.trlKwf('not activated').'</span>';
        } else if (!$row->getMailUnsubscribe() && $row->activated) {
            return '<span class="active">'.trlKwf('active').'</span>';
        }
    }
}
