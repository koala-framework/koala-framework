<?php
class Vps_Controller_Action_User_Users_LogMessageData extends Vps_Data_Abstract
{
    public function load($row)
    {
        return $row->__toString();
    }
}
