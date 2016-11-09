<?php
class Kwc_Newsletter_StartMaintenanceJob extends Kwf_Util_Maintenance_Job_Abstract
{
    private $_procs = array();

    public function getFrequency()
    {
        return self::FREQUENCY_SECONDS;
    }

    public function execute($debug)
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Model');

        $select = $model->select()
            ->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('status', 'start'),
                new Kwf_Model_Select_Expr_And(array(
                    new Kwf_Model_Select_Expr_Equal('status', 'startLater'),
                    new Kwf_Model_Select_Expr_LowerEqual('start_date', new Kwf_DateTime(time())),
                )),
                new Kwf_Model_Select_Expr_Equal('status', 'sending')
            )));
        $rows = $model->getRows($select);
        $activeCountRows = count($rows);
        foreach ($rows as $newsletterRow) {

            if ($newsletterRow->status != 'sending') {
                $newsletterRow->resume_date = date('Y-m-d H:i:s');
                $newsletterRow->status = 'sending';
                if (is_null($newsletterRow->count_sent)) $newsletterRow->count_sent = 0;
                $newsletterRow->save();
            }

            if (!isset($this->_procs[$newsletterRow->id])) {
                $this->_procs[$newsletterRow->id] = array();
            }

            //remove stopped processes (might stop because of memory limit or simply crash for some reason)
            foreach ($this->_procs[$newsletterRow->id] as $k=>$p) {
                if (!$p->isRunning()) {
                    if ($debug) echo "process ".$p->getPid()." stopped...\n";
                    unset($this->_procs[$newsletterRow->id][$k]);
                }
            }

            $s = new Kwf_Model_Select();
            $s->whereEquals('newsletter_id', $newsletterRow->id);
            $s->whereNull('send_process_pid');
            if (!$newsletterRow->getModel()->getDependentModel('Queue')->countRows($s)) {
                $newsletterRow->status = 'finished';
                $newsletterRow->save();
                if ($debug) echo "Newsletter finished.\n";

                //give send processes time to finish
                sleep(5);

                //delete "hanging" queue entries
                $s = new Kwf_Model_Select();
                $s->whereEquals('newsletter_id', $newsletterRow->id);
                foreach ($newsletterRow->getModel()->getDependentModel('Queue')->getRows($s) as $queueRow) {
                    $newsletterRow->getModel()->getDependentModel('QueueLog')->createRow(array(
                        'newsletter_id' => $queueRow->newsletter_id,
                        'recipient_model' => $queueRow->recipient_model,
                        'recipient_id' => $queueRow->recipient_id,
                        'status' => 'failed',
                        'send_date' => date('Y-m-d H:i:s')
                    ))->save();
                    $msg = "Newsletter finished but queue entry with pid $queueRow->send_process_pid still exists: $queueRow->recipient_id $queueRow->searchtext";
                    $e = new Kwf_Exception($msg);
                    $e->logOrThrow();
                    echo $msg."\n";
                    $queueRow->delete();
                }
                continue;
            }

            if ($debug) {
                echo count($this->_procs[$newsletterRow->id])." running processes\n";
            }

            $numOfProcesses = 1;
            if ($newsletterRow->mails_per_minute == 'unlimited') {
                $numOfProcesses = 3;
            }
            while (count($this->_procs[$newsletterRow->id]) < $numOfProcesses) {
                $cmd = "php bootstrap.php newsletter send --newsletterId=$newsletterRow->id";
                if ($debug) $cmd .= " --debug";
                //if ($this->_getParam('benchmark')) $cmd .= " --benchmark";
                //if ($this->_getParam('verbose')) $cmd .= " --verbose";
                $descriptorspec = array(
                    1 => STDOUT,
                    2 => STDERR,
                );
                $p = new Kwf_Util_Proc($cmd, $descriptorspec);
                $this->_procs[$newsletterRow->id][] = $p;
                if ($debug) {
                    echo "\n*** started new process with PID ".$p->getPid()."\n";
                    echo $cmd."\n";
                }
                sleep(3); //don't start all processes at the same time
            }

            if ($debug) {
                echo "Newletter $newsletterRow->id: currently sending with ".
                    round($newsletterRow->getCurrentSpeed()).
                    " mails/min\n";
            }
        }
    }
}
