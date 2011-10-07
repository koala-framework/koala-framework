<?php
class Vps_Log_Formatter_Html implements Zend_Log_Formatter_Interface
{
    public function format($event)
    {
        if ($event['priorityName'] == 'INFO') {
            $tag = 'h5';
        } else if ($event['priorityName'] == 'DEBUG') {
            $tag = 'pre';
        } else {
            $tag = 'h4';
        }
        return "<$tag>$event[message]</$tag>";
    }
}
