<?php
class Kwf_Controller_Action_Enquiries_EnquiryFromData extends Kwf_Data_Abstract
{
    public function load($row, array $info)
    {
        $from = $row->getFrom();
        if (!$from) return '';
        return $from['email'];
    }
}
