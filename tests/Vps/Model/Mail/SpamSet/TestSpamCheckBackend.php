<?php
class Vps_Model_Mail_SpamSet_TestSpamCheckBackend implements Vps_Util_Check_Spam_Backend_Interface
{
    public function checkIsSpam($text)
    {
        return true;
    }
}