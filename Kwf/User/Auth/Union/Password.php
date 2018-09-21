<?php
class Kwf_User_Auth_Union_Password extends Kwf_User_Auth_Union_Abstract implements Kwf_User_Auth_Interface_Password
{
    public function getRowByIdentity($identity)
    {
        $row = $this->_auth->getRowByIdentity($identity);
        if (!$row) return null;

        foreach ($this->_model->getUnionModels() as $k=>$m) {
            if ($m == $row->getModel()) {
                $id = $k.$row->{$m->getPrimaryKey()};
                return $this->_model->getRow($id);
            }
        }
        return null;
    }

    public function validatePassword(Kwf_Model_Row_Interface $row, $password)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->validatePassword($row->getSourceRow(), $password);
        } else {
            return null;
        }
    }

    public function setPassword(Kwf_Model_Row_Interface $row, $password)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->setPassword($row->getSourceRow(), $password);
        } else {
            return false;
        }
    }

    public function sendLostPasswordMail(Kwf_Model_Row_Interface $row, Kwf_User_Row $kwfUserRow)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->sendLostPasswordMail($row->getSourceRow(), $kwfUserRow);
        } else {
            return false;
        }
    }
}
