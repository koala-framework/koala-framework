<?php
/**
 * @group slow
 * @group Kwc_Newsletter
 */
class Kwc_Newsletter_Test extends Kwf_Test_TestCase
{
    public function testMailSending()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_TestModel');
        $logModel = $model->getDependentModel('Log');
        $queueModel = $model->getDependentModel('Queue');

        // Vom Newsletter mit der ID 2 sollten zwei Einträge gesendet werden
        $model->send(0);
        $this->assertEquals(1, $logModel->countRows());
        $this->assertEquals(2, $logModel->getRow(1)->count);
        $this->assertEquals(0, $logModel->getRow(1)->countErrors);
        $this->assertEquals(2, $logModel->getRow(1)->newsletter_id);
        $this->assertEquals(0, $queueModel->countRows($queueModel->select()
            ->whereEquals('newsletter_id', 2)
        ));
        $this->assertEquals(2, $model->getRow(2)->count_sent);
        return;

        // Vom Newsletter mit der ID 1 sollte ein Eintrag gesendet werden
        $model->send(0);
        $this->assertEquals(2, $logModel->countRows());
        $this->assertEquals(1, $logModel->getRow(2)->count);
        $this->assertEquals(1, $logModel->getRow(2)->newsletter_id);
        $this->assertEquals(2, $model->getRow(2)->count_sent);
        $this->assertEquals(1, $queueModel->countRows($queueModel->select()
            ->whereEquals('status', 'queued')
        ));

        // Nichts mehr zu senden
        $model->send(0);
        $this->assertEquals(2, $logModel->countRows());
        $this->assertEquals(0, $queueModel->countRows());
    }
}
