<?php
class Kwf_Model_Mail_SpamSet_TestSpamCheckBackend implements Kwf_Util_Check_Spam_Backend_Interface
{
    public function checkIsSpam($text)
    {
        return true;
    }
}