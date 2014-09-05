<?php
class Kwf_User_Auth_PasswordFields extends Kwf_User_Auth_Abstract implements Kwf_User_Auth_Interface_Password
{
    private $_mailTransport = null;

    public function getRowByIdentity($identity)
    {
        $s = new Kwf_Model_Select();
        if (is_numeric($identity)) {
            //for cookie login
            $s->whereEquals('id', $identity);
        } else {
            $s->whereEquals('email', $identity);
        }
        $s->whereEquals('deleted', false);
        $s->whereEquals('locked', false);
        return $this->_model->getRow($s);
    }

    private function _encodePassword(Kwf_Model_Row_Interface $row, $password)
    {
        return md5($password.$row->password_salt);
    }

    //not part of interface but used by Kwf_User_EditRow
    public function generatePasswordSalt(Kwf_Model_Row_Interface $row)
    {
        mt_srand((double)microtime()*1000000);
        $row->password_salt = substr(md5(uniqid(mt_rand(), true)), 0, 10);
    }

    public function validatePassword(Kwf_Model_Row_Interface $row, $password)
    {
        if ($this->_encodePassword($row, $password) == $row->password) {
            return true;
        }
        if ($password == Kwf_Util_Hash::hash($row->password.$row->password_salt)) { // for cookie login
            return true;
        }
        return false;
    }

    public function setPassword(Kwf_Model_Row_Interface $row, $password)
    {
        $this->generatePasswordSalt($row);
        $row->password = $this->_encodePassword($row, $password);
        return true;
    }

    public function getActivationCode(Kwf_Model_Row_Interface $row)
    {
        return substr(md5($row->password_salt), 0, 10);
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
}
