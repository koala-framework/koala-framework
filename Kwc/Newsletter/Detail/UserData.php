<?php
class Kwc_Newsletter_Detail_UserData extends Kwf_Data_Abstract
{
    private $_fieldname;

    public function __construct($fieldname)
    {
        $this->_fieldname = $fieldname;
    }

    public function load($row, array $info = array())
    {
        $recipient = $row->getRecipient();
        if ($recipient) {
            $method = 'getMail' . ucfirst($this->_fieldname);
            return $recipient->$method();
        }
        return '';
    }
}
