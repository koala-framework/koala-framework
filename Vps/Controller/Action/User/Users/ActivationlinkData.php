<?php
class Vps_Controller_Action_User_Users_ActivationlinkData extends Vps_Auto_Data_Abstract
{

    public function load($row)
    {
        if (empty($row->password)) {
            return '<a href="/vps/user/login/activate?code='.$row->id.'-'.$row->getActivationCode()
                .'" target="_blank">Hier klicken</a>';
        } else {
            return 'Bereits aktiviert';
        }
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $data)
    {
    }

}
