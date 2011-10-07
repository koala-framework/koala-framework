<?php
class Vps_Controller_Action_User_Users_ActivationlinkData extends Vps_Data_Abstract
{

    public function load($row)
    {
        if (empty($row->password)) {
            return '<a href="/vps/user/login/activate?code='.$row->id.'-'.$row->getActivationCode()
                .'" target="_blank">'.trlVps('Click here').'</a>';
        } else {
            return trlVps('Already activated');
        }
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
    }

}
