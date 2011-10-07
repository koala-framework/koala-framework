<?php
class Vps_Controller_Action_User_Users_WebcodeData extends Vps_Data_Abstract
{

    public function load($row)
    {
        if (empty($row->webcode)) {
            return 0;
        } else {
            return 1;
        }
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        $acl = Zend_Registry::get('acl');
        if ($acl->getRole($authedRole) instanceof Vps_Acl_Role_Admin) {
            if (!$data) {
                $row->webcode = '';
            } else if ($data && $row->webcode !== '') {
                // webcode holen
                $row->webcode = Zend_Registry::get('userModel')->getWebcode();
            }
        }
    }

}
