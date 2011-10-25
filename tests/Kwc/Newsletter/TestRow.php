<?php
class Kwc_Newsletter_TestRow extends Kwc_Newsletter_Row
{
    protected function _sendMail($recipient)
    {
        return true;
    }

}