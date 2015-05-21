<?php
class Kwf_User_Auth_AutoLoginFields extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_AutoLogin
{
    public function getRowById($id)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', $id);
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
        $rounds = '08';
        $string = $this->_getHashHmacStringForBCrypt($row, $token);
        $salt = substr ( str_shuffle ( './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) , 0, 22 );
        return crypt ( $string, '$2a$' . $rounds . '$' . $salt );
    }

    public function validateAutoLoginToken(Kwf_Model_Row_Interface $row, $token)
    {
        if (!$row->autologin) return false;
        $autologin = explode(':', $row->autologin);
        $expire = $autologin[0];
        $rowToken = $autologin[1];
        if ($expire < time()) return false;
        if ($this->_validateTokenBcrypt($row, $token) === $rowToken) {
            return true;
        }
        return false;
    }

    private function _validateTokenBcrypt($row, $token)
    {
        $loginToken = explode(':', $row->autologin);
        $string = $this->_gethashHmacStringForBCrypt($row, $token);
        return crypt($string, substr($loginToken[1], 0, 30));
    }
    private function _getHashHmacStringForBCrypt(Kwf_Model_Row_Interface $row, $password)
    {
        $globalSalt = Kwf_Registry::get('config')->user->passwordSalt;
        return hash_hmac ( "whirlpool", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $row->id ), STR_PAD_BOTH ), $globalSalt, true );
    }
}
