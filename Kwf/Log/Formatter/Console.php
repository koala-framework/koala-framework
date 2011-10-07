<?php
class Vps_Log_Formatter_Console implements Zend_Log_Formatter_Interface
{
    public function format($event)
    {
        if ($event['priorityName'] == 'INFO') {
            $pre = "\n====>";
        } else if ($event['priorityName'] == 'DEBUG') {
            $pre = '';
        } else {
            $pre = '';
        }
        return "$pre$event[message]\n";
    }
}
