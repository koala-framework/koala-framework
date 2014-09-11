<?php
class Kwf_User_Auth_AutoLoginFields extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_AutoLogin
{
    public function getRowById($id)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', $id);
        $s->whereEquals('locked', false);
        return $this->_model->getRow($s);
    }

    public function clearAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        $row->autologin = null;
        $row->save();
        return true;
    }

    public function generateAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        $token = Kwf_Util_Hash::hash(microtime(true).uniqid('', true).mt_rand());
        $expire = time()+100*24*60*60;
        $row->autologin = $expire.':'.$this->_encodeToken($row, $token);
        $row->save();
        return $token;
    }

    private function _encodeToken(Kwf_Model_Row_Interface $row, $token)
    {
        return md5($token.$row->password_salt);
    }

    public function validateAutoLoginToken(Kwf_Model_Row_Interface $row, $token)
    {
        if (!$row->autologin) return false;
        $autologin = explode(':', $row->autologin);
        $expire = $autologin[0];
        $rowToken = $autologin[1];
        if ($expire < time()) return false;
        if ($this->_encodeToken($row, $token) == $rowToken) {
            return true;
        }

        return false;
    }
}
