<?php
/**
 * Auth Method Interface for auto logging in users by auto login cookie
 */
interface Kwf_User_Auth_Interface_AutoLogin
{
    public function getRowById($id);
    public function generateAutoLoginToken(Kwf_Model_Row_Interface $row);
    public function clearAutoLoginToken(Kwf_Model_Row_Interface $row);
    public function validateAutoLoginToken(Kwf_Model_Row_Interface $row, $token);
}
