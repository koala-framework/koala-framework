<?php
class Vpc_Newsletter_TestRow extends Vpc_Newsletter_Row
{
    protected function _sendMail($recipient)
    {
        return true;
    }

}