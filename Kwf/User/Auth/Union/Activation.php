<?php
class Kwf_User_Auth_Union_Activation extends Kwf_User_Auth_Union_Abstract implements Kwf_User_Auth_Interface_Activation
{
    public function validateActivationToken(Kwf_Model_Row_Interface $row, $token)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->validateActivationToken($row->getSourceRow(), $token);
        } else {
            return null;
        }
    }

    public function generateActivationToken(Kwf_Model_Row_Interface $row, $type)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->generateActivationToken($row->getSourceRow(), $type);
        } else {
            return null;
        }
    }

    public function isActivated(Kwf_Model_Row_Interface $row)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->isActivated($row->getSourceRow());
        } else {
            return null;
        }
    }

    public function clearActivationToken(Kwf_Model_Row_Interface $row)
    {
        return $this->_auth->clearActivationToken($row->getSourceRow());
    }
}
