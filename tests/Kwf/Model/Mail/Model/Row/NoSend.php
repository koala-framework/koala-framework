<?php
class Kwf_Model_Mail_Model_Row_NoSend extends Kwf_Model_Mail_Row
{
    protected function _afterInsert()
    {
        // vermeidet mail senden
    }
}
