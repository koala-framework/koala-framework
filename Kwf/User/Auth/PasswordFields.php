<?php
/**
 * Implements login by username/password
 *
 * Required model fields:
 * - email
 * - password
 * - password_salt
 */
class Kwf_User_Auth_PasswordFields extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_Password
{
    private $_mailTransport = null;
    private $_passwordHashMethod = 'bcrypt';

    public function getRowByIdentity($identity)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('email', $identity);
        return $this->_model->getRow($s);
    }

    private function _encodePasswordMd5(Kwf_Model_Row_Interface $row, $password)
    {
        return md5($password.$row->password_salt);
    }
    private function _encodePasswordBcrypt(Kwf_Model_Row_Interface $row, $password)
    {
        $rounds = '08';
        $string = $this->_getHashHmacStringForBCrypt($row, $password);
        $salt = substr ( str_shuffle ( './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) , 0, 22 );
        return crypt ( $string, '$2a$' . $rounds . '$' . $salt );
    }

    //not part of interface but used by Kwf_User_EditRow
    public function generatePasswordSalt(Kwf_Model_Row_Interface $row)
    {
        mt_srand((double)microtime()*1000000);
        $row->password_salt = substr(md5(uniqid(mt_rand(), true)), 0, 10);
    }

    public function validatePassword(Kwf_Model_Row_Interface $row, $password)
    {
        if (preg_match('#^\$2a\$#', $row->password)) {
            if ($this->_validatePasswordBcrypt($row, $password) === $row->password) {
                return true;
            }
        } else {
            if ($this->_encodePasswordMd5($row, $password) === $row->password) {
                return true;
            }
        }
        return false;
    }

    public function setPassword(Kwf_Model_Row_Interface $row, $password)
    {
        if ($this->getPasswordHashMethod() == 'bcrypt') {
            $row->password = $this->_encodePasswordBcrypt($row, $password);
        } else if ($this->getPasswordHashMethod() == 'md5') {
            $this->generatePasswordSalt($row);
            $row->password = $this->_encodePasswordMd5($row, $password);
        } else {
            throw new Kwf_Exception_NotYetImplemented('hashing type not yet implemented');
        }
        $row->activate_token = '';
        return true;
    }

    public function setMailTransport($value)
    {
        $this->_mailTransport = $value;
    }

    public function sendLostPasswordMail(Kwf_Model_Row_Interface $row, Kwf_User_Row $kwfUserRow)
    {
        $mail = new Kwf_User_Mail_LostPassword($kwfUserRow);
        $mail->send($this->_mailTransport);
        if ($row instanceof Kwf_User_EditRow) {
            $row->getModel()->writeLog(array(
                'user_id' => $row->id,
                'message_type' => 'user_mail_UserLostPassword'
            ));
        }
        return true;
    }

    public function setPasswordHashMethod($method)
    {
        $this->_passwordHashMethod = $method;
    }

    public function getPasswordHashMethod()
    {
        return $this->_passwordHashMethod;
    }


    private function _getHashHmacStringForBCrypt(Kwf_Model_Row_Interface $row, $password)
    {
        $globalSalt = Kwf_Registry::get('config')->user->passwordSalt;
        return hash_hmac ( "whirlpool", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $row->id ), STR_PAD_BOTH ), $globalSalt, true );
    }

    private function _validatePasswordBcrypt($row, $password)
    {
        $string = $this->_getHashHmacStringForBCrypt($row, $password);
        return crypt($string, substr($row->password, 0, 30));
    }
}
