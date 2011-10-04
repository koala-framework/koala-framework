<?php
class Vps_Model_Mail_Model_Row_NoSend extends Vps_Model_Mail_Row
{
    protected function _afterInsert()
    {
        // vermeidet mail senden
    }
}
