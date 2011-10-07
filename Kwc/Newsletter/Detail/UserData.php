<?php
class Vpc_Newsletter_Detail_UserData extends Vps_Data_Abstract
{
    private $_fieldname;

    public function __construct($fieldname)
    {
        $this->_fieldname = $fieldname;
    }

    public function load($row)
    {
        $recipient = $row->getRecipient();
        if (!$recipient) {
            $row->status = 'userNotFound';
            $row->save();
            return '';
        } else {
            $method = 'getMail' . ucfirst($this->_fieldname);
            return $recipient->$method();
        }
    }
}
