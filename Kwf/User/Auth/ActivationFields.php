<?php
/**
 * Implements an activation token that is saved to activate_token field in user model.
 *
 * Token times out after 24h
 */
class Kwf_User_Auth_ActivationFields extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_Activation
{
    private function _encodePasswordBcrypt(Kwf_Model_Row_Interface $row, $password)
    {
        $rounds = '08';
        $string = $this->_getHashHmacStringForBCrypt($row, $password);
        $salt = substr ( str_shuffle ( './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) , 0, 22 );
        return crypt ( $string, '$2a$' . $rounds . '$' . $salt );
    }

    private function _getHashHmacStringForBCrypt(Kwf_Model_Row_Interface $row, $password)
    {
        $globalSalt = Kwf_Registry::get('config')->user->passwordSalt;
        return hash_hmac ( "whirlpool", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $row->id ), STR_PAD_BOTH ), $globalSalt, true );
    }

    private function _validateActivateTokenBcrypt($row, $token)
    {
        $activateToken = explode(':', $row->activate_token);
        $string = $this->_getHashHmacStringForBCrypt($row, $token);
        return crypt($string, substr($activateToken[1], 0, 30));
    }

    protected function _getValidityDurationInDays($type)
    {
        return ($type == self::TYPE_ACTIVATE) ? 7 : 1;
    }

    public function validateActivationToken(Kwf_Model_Row_Interface $row, $token)
    {
        if (!$row->activate_token) return false;
        $activateToken = explode(':', $row->activate_token);
        $expire = $activateToken[0];
        $rowToken = $activateToken[1];
        if ($expire < time()) return false;
        if ($this->_validateActivateTokenBcrypt($row, $token) === $rowToken) {
            return true;
        }
        return false;
    }

    public function generateActivationToken(Kwf_Model_Row_Interface $row, $type)
    {
        $token = Kwf_Util_Hash::hash(microtime(true).uniqid('', true).mt_rand());
        $days = $this->_getValidyDurationInDays($type);
        $expire = time()+$days*24*60*60;
        $row->activate_token = $expire.':'.$this->_encodePasswordBcrypt($row, $token);
        $row->save();
        return $token;
    }

    public function isActivated(Kwf_Model_Row_Interface $row)
    {
        return !$row->activate_token;
    }

    public function clearActivationToken(Kwf_Model_Row_Interface $row)
    {
        $row->activate_token = null;
        $row->save();
        return true;
    }
}
