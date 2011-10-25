<?php
class Kwf_Controller_Action_User_Users_LogMessageData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        return $row->__toString();
    }
}
