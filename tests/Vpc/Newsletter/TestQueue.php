<?php
class Vpc_Newsletter_TestQueue extends Vpc_Newsletter_Queue
{
    protected $_model = 'Vpc_Newsletter_TestQueueModel';
    protected $_logModel = 'Vpc_Newsletter_TestQueueLogModel';
    protected $_nlModel = 'Vpc_Newsletter_TestModel';

    protected function sendMail($nlRow, $recipient)
    {
        return true;
    }
}
