<?php
class Vps_Model_User_User extends Zend_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->realname;
    }

    public function generateNewPassword()
    {
        $newPassword = substr(md5(uniqid(mt_rand(), true)), 0, 6);
        if (!$this->password_salt) {
            mt_srand((double)microtime()*1000000);
            $this->password_salt = substr(md5(uniqid(mt_rand(), true)), 0, 10);
        }
        $this->password = md5($newPassword.$this->password_salt);
        $this->password_isnew = 1;
        $this->password_mailed = 0;
        return $newPassword;
    }

    /**
     * Erstellt ein neues Passwort und sendet es per Mail an den User
     */
    public function sendPasswordMail()
    {
        $newPassword = $this->generateNewPassword();
        if ($this->email) {
            $this->password_mailed = 1;
            $mail = new Zend_Mail('utf-8');
            //todo: smarty template verwenden für mailtext
            $bodyText = "Hallo ".$this->__toString()."!\n\n"
                ."Folgendes Login ist ab sofort für Sie aktiv.\n\n"
                ."Benutzername: ".$this->username."\n"
                ."Passwort: ".$newPassword."\n\n"
                ."---\nDiese Email wurde automatisch erstellt - bitte nicht antworten.";
            $mail->setBodyText($bodyText);
            $mail->setFrom('noreply@vivid-planet.com', 'Vivid Planet Software');
            $mail->addTo($this->email, $this->__toString());
            $mail->setSubject('Ihr Account');
            $mail->send();
            return true;
        }
        return false;
    }

    public function toArray()
    {
        $user = parent::toArray();
        unset($user['password'], $user['password_salt']);
        return $user;
    }
}
