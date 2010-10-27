<?php
class Vpc_Newsletter_Detail_IsActiveData extends Vps_Data_Abstract
{
    public function load($row)
    {
        if ($row->getMailUnsubscribe() && $row->activated) {
            return '<span class="unsubscribed">'.trlVps('unsubscribed').'</span>';
        } else if ($row->getMailUnsubscribe() && !$row->activated) {
            return '<span class="inactive">'.trlVps('not activated').'</span>';
        } else if (!$row->getMailUnsubscribe() && $row->activated) {
            return '<span class="active">'.trlVps('active').'</span>';
        }
    }
}
