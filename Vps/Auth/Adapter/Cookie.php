<?php

require_once 'Zend/Auth/Adapter/Interface.php';

class Vps_Auth_Adapter_Cookie implements Zend_Auth_Adapter_Interface
{
    protected $_identity = null;
    protected $_credential = null;

    protected $_userId = null;

    /**
     * @param string $identd Muss die ID des Benutzers sein
     */
    public function setIdentity($identd)
    {
        $this->_identity = $identd;
        return $this;
    }

    /**
     * @param string $credential Muss ein md5 von password_salt des Benutzers sein
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function authenticate()
    {
        if (empty($this->_identity)) {
            throw new Vps_Exception('A value for the identity was not provided prior to authentication with Vps_Auth_Adapter_Service.');
        } else if ($this->_credential === null) {
            throw new Vps_Exception('A credential value was not provided prior to authentication with Vps_Auth_Adapter_Service.');
        }

        $userModel = Zend_Registry::get('userModel');
        $userRow = $userModel->fetchRow(array('id = ?' => $this->_identity));

        if ($userRow) {
            if ($this->_credential == md5($userRow->password_salt)) {
                $this->_userId = $userRow->id;
                $userRow->last_login = date('Y-m-d H:i:s');
                $userRow->logins = $userRow->logins + 1;
                $userRow->save();
                return new Zend_Auth_Result(
                    Zend_Auth_Result::SUCCESS,
                    $userRow->email,
                    array('Authentication successful.')
                );
            } else {
                return new Zend_Auth_Result(
                    Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                    $userRow->email,
                    array('Supplied cookie is invalid.')
                );
            }
        } else {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                $this->_identity,
                array('User not existent in this web.')
            );
        }
    }

}