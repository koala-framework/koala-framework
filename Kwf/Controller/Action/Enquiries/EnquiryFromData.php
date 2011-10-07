<?php
class Vps_Controller_Action_Enquiries_EnquiryFromData extends Vps_Data_Abstract
{
    public function load($row)
    {
        $from = $row->getFrom();
        if (!$from) return '';
        return $from['email'];
    }
}