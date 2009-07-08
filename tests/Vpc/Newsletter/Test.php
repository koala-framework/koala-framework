<?php
/**
 * @group Vpc_Newsletter
 */
class Vpc_Newsletter_Test extends PHPUnit_Framework_TestCase
{
    public function testMailSending()
    {
        $queue = new Vpc_Newsletter_TestQueue();
        $logModel = Vps_Model_Abstract::getInstance('Vpc_Newsletter_TestQueueLogModel');
        $queueModel = Vps_Model_Abstract::getInstance('Vpc_Newsletter_TestQueueModel');

        // Vom Newsletter mit der ID 2 sollten zwei Einträge gesendet werden
        $queue->send(6);
        $this->assertEquals(1, $logModel->countRows());
        $this->assertEquals(2, $logModel->getRow(1)->count);
        $this->assertEquals(0, $logModel->getRow(1)->countErrors);
        $this->assertEquals(2, $logModel->getRow(1)->newsletter_id);
        $this->assertEquals('userNotFound', $queueModel->getRow(6)->status);
        $this->assertEquals(2, $queueModel->countRows($queueModel->select()
            ->whereEquals('status', 'sent')
            ->whereEquals('newsletter_id', 2)
        ));
        $this->assertEquals(1, $queueModel->countRows($queueModel->select()
            ->whereEquals('status', 'sent')
            ->whereEquals('newsletter_id', 1)
        ));

        // Vom Newsletter mit der ID 1 sollte ein Eintrag gesendet werden
        $queue->send(6);
        $this->assertEquals(2, $logModel->countRows());
        $this->assertEquals(1, $logModel->getRow(2)->count);
        $this->assertEquals(1, $logModel->getRow(2)->newsletter_id);
        $this->assertEquals(2, $queueModel->countRows($queueModel->select()
            ->whereEquals('status', 'sent')
            ->whereEquals('newsletter_id', 1)
        ));
        $this->assertEquals(1, $queueModel->countRows($queueModel->select()
            ->whereEquals('status', 'queued')
        ));

        // Nichts mehr zu senden
        $queue->send(6);
        $this->assertEquals(2, $logModel->countRows());
    }
}
