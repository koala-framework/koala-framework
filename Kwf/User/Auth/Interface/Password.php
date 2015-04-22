<?php
/**
 * Auth Method Interface for logging in users by password
 */
interface Kwf_User_Auth_Interface_Password
{
    public function getRowByIdentity($identity);
    public function validatePassword(Kwf_Model_Row_Interface $row, $password);
    public function setPassword(Kwf_Model_Row_Interface $row, $password);
    public function sendLostPasswordMail(Kwf_Model_Row_Interface $row, Kwf_User_Row $kwfUserRow);
}
