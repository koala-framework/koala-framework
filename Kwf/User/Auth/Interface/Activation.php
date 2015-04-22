<?php
/**
 * Auth Method Interface for initial activating users by an activation token
 */
interface Kwf_User_Auth_Interface_Activation
{
    const TYPE_ACTIVATE = 'activate';
    const TYPE_LOSTPASSWORD = 'lostpassword';

    public function validateActivationToken(Kwf_Model_Row_Interface $row, $token);
    public function generateActivationToken(Kwf_Model_Row_Interface $row, $type);
    public function isActivated(Kwf_Model_Row_Interface $row);
    public function clearActivationToken(Kwf_Model_Row_Interface $row);
}
