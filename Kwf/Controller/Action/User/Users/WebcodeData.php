<?php
class Kwf_Controller_Action_User_Users_WebcodeData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        if (empty($row->webcode)) {
            return 0;
        } else {
            return 1;
        }
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
    }
}
