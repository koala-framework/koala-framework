<?php
class Vps_User_User extends Zend_Db_Table_Row_Abstract
{
    public function generateNewPassword()
    {
        $newPassword = substr(md5(uniqid(mt_rand(), true)), 0, 6);
        if (!$this->password_salt) {
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
            $mailtext = "Hallo ".$this->realname."!\n\n"
                ."Folgendes Login ist ab sofort für Sie in der Ärzteverwaltung aktiv.\n\n"
                ."Benutzername: ".$this->username."\n"
                ."Passwort: ".$newPassword."\n\n"
                ."---\nDiese Email wurde automatisch erstellt - bitte nicht antworten.";
            return mail($this->email, 'Ihr Account in der Ärzteverwaltung', $mailtext, 'From: no-reply@aerzteverwaltung.de');
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