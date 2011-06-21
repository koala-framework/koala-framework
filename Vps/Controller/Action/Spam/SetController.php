<?php
class Vps_Controller_Action_Spam_SetController extends Vps_Controller_Action
{
    public function indexAction()
    {
        // wenn zuvor als Spam (-verdacht) markiert und dann als ham reinkommt,
        // ham setzen und email doch schicken
        $value = $this->_getParam('value');
        if ($value == 0) {
            $row = Vps_Model_Abstract::getInstance('Vps_Model_Mail')->getRow($this->_getParam('id'));
            if (!$row) die('0');

            if (Vps_Model_Mail_Row::getSpamKey($row) != $this->_getParam('key')) {
                die('0');
            }

            if (!$row->mail_sent) {
                $row->is_spam = 0;
                $result = $row->sendMail();
                die('1');
            }
        }
        die('0');
    }
}
