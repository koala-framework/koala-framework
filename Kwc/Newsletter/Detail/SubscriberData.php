<?php
class Kwc_Newsletter_Detail_SubscriberData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        if ($row instanceof Kwc_Mail_Recipient_Interface) {
            if ($this->getFieldname() == 'firstname') {
                return $row->getMailFirstname();
            } else if ($this->getFieldname() == 'lastname') {
                return $row->getMailLastname();
            } else if ($this->getFieldname() == 'email') {
                return $row->getMailEmail();
            }
        }
    }
}
