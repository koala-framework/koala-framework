<?php
class Kwf_Controller_Action_User_Users_ActivationlinkData extends Kwf_Data_Abstract
{

    public function load($row)
    {
        if (empty($row->password)) {
            return '<a href="/kwf/user/login/activate?code='.$row->id.'-'.$row->getActivationCode()
                .'" target="_blank">'.trlKwf('Click here').'</a>';
        } else {
            return trlKwf('Already activated');
        }
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
    }

}
