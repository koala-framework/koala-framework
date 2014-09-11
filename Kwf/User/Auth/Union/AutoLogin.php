<?php
class Kwf_User_Auth_Union_AutoLogin extends Kwf_User_Auth_Proxy_Abstract
{
    public function getRowById($id)
    {
        $row = $this->_auth->getRowById($id);
        if (!$row) return null;

        foreach ($this->_model->getUnionModels() as $k=>$m) {
            if ($m == $row->getModel()) {
                $i = $k.$row->{$m->getPrimaryKey()};
                return $this->_model->getRowById($i);
            }
        }
        return null;
    }

    public function clearAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->clearAutoLoginToken($row->getSourceRow());
        } else {
            return null;
        }
    }

    public function generateAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->generateAutoLoginToken($row->getSourceRow());
        } else {
            return null;
        }
    }

    public function validateAutoLoginToken(Kwf_Model_Row_Interface $row, $token)
    {
        if ($row->getSourceRow()->getModel() == $this->_auth->_model) {
            return $this->_auth->validateAutoLoginToken($row->getSourceRow(), $token);
        } else {
            return false;
        }
    }
}
