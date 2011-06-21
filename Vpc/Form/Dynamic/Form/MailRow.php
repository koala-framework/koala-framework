<?php
class Vpc_Form_Dynamic_Form_MailRow extends Vps_Model_Mail_Row
{
    protected function _afterInsert()
    {
        Vps_Model_Proxy_Row::_afterInsert(); //nicht parent!!

        //_buildContentAndSetToRow() hier nicht aufrufen, weil wir den contents selbst setzen (in der Component.php)
        $this->is_spam = $this->_checkIsSpam();
        //sendMail() hier nicht aufrufen, da wir das selbst aufrufen nachdem der content gesetzt wurde
    }
}
