<?php
/**
 * @internal
 * Encrypt Helper functions related to auth-methods
 */
class Kwf_User_Auth_Bcrypt
{
    /**
     * @internal
     */
    public static function validateValue(Kwf_Model_Row_Interface $row, $value, $salt)
    {
        $string = self::_getHashHmacString($row, $value);
        return crypt($string, substr($salt, 0, 30));
    }

    /**
     * @internal
     */
    public static function encodeValue(Kwf_Model_Row_Interface $row, $value)
    {
        $rounds = '08';
        $string = self::_getHashHmacString($row, $value);
        $salt = substr ( str_shuffle ( './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) , 0, 22 );
        return crypt ( $string, '$2a$' . $rounds . '$' . $salt );
    }

    private static function _getHashHmacString(Kwf_Model_Row_Interface $row, $password)
    {
        $globalSalt = Kwf_Registry::get('config')->user->passwordSalt;
        return hash_hmac ( "whirlpool", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $row->id ), STR_PAD_BOTH ), $globalSalt, true );
    }
}
