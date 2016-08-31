<?php
class Kwf_Controller_Action_User_Users_LogMessageData extends Kwf_Data_Abstract
{
    public function load($row, array $info)
    {
        return $row->__toString();
    }
}
