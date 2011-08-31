<?php
class Vps_Controller_Action_Spam_SetController extends Vps_Controller_Action
{
    public function indexAction()
    {
        // wenn zuvor als Spam (-verdacht) markiert und dann als ham reinkommt,
        // ham setzen und email doch schicken
        $value = $this->_getParam('value');
        if ($value == 0) {
            if (self::sendSpammedMail($this->_getParam('id'), $this->_getParam('key'))) {
                die('1');
            } else {
                die('0');
            }
        }
        die('0');
    }

    /**
     * Public only for testing!!!
     */
    public static function sendSpammedMail($id, $key)
    {
        $row = Vps_Model_Abstract::getInstance('Vps_Model_Mail')->getRow($id);
        if (!$row) return false;

        if (Vps_Util_Check_Spam::getSpamKey($row) != $key) {
            return false;
        }

        if (!$row->mail_sent) {
            $row->is_spam = 0;
            $row->sendMail(); // setzt mail_sent auf 1 und speichert
            return true;
        }
        return false;
    }
}
