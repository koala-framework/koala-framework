<?php
class Kwf_Controller_Action_Cli_Web_NewsletterController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "Call by cronjob to send waiting newsletters. If called manually, Ctrl+C stops newsletter, ist has to be started again!";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'debug',
                'value'=> false,
                'valueOptional' => true,
            )
        );
    }
    /*
    possible parameters:
    --maxProcesses
    --debug
    --benchmark
    */

    public function indexAction()
    {
        throw new Kwf_Exception_Client("Don't call Newsletter controller directly. Use process-control to start 'php bootstrap.php newsletter start' instead.");
    }

    public function startAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Model');

        $procs = array();

        while (true) {
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
                    $newsletterRow->save();
                }

                if (!isset($procs[$newsletterRow->id])) {
                    $procs[$newsletterRow->id] = array();
                }

                //remove stopped processes (might stop because of memory limit or simply crash for some reason)
                foreach ($procs[$newsletterRow->id] as $k=>$p) {
                    if (!$p->isRunning()) {
                        if ($this->_getParam('debug')) echo "process ".$p->getPid()." stopped...\n";
                        unset($procs[$newsletterRow->id][$k]);
                    }
                }

                $s = new Kwf_Model_Select();
                $s->whereEquals('newsletter_id', $newsletterRow->id);
                $s->whereNull('send_process_pid');
                if (!$newsletterRow->getModel()->getDependentModel('Queue')->countRows($s)) {
                    $newsletterRow->status = 'finished';
                    $newsletterRow->save();
                    if ($this->_getParam('debug')) echo "Newsletter finished.\n";

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

                if ($this->_getParam('debug')) {
                    echo count($procs[$newsletterRow->id])." running processes\n";
                }

                $numOfProcesses = 1;
                if ($newsletterRow->mails_per_minute == 'unlimited') {
                    $numOfProcesses = $this->_getParam('maxProcesses');
                    if (!$numOfProcesses) $numOfProcesses = 3;
                }
                while (count($procs[$newsletterRow->id]) < $numOfProcesses) {
                    $cmd = "php bootstrap.php newsletter send --newsletterId=$newsletterRow->id";
                    if ($this->_getParam('debug')) $cmd .= " --debug";
                    if ($this->_getParam('benchmark')) $cmd .= " --benchmark";
                    if ($this->_getParam('verbose')) $cmd .= " --verbose";
                    $descriptorspec = array(
                        1 => STDOUT,
                        2 => STDERR,
                    );
                    $p = new Kwf_Util_Proc($cmd, $descriptorspec);
                    $procs[$newsletterRow->id][] = $p;
                    if ($this->_getParam('debug')) {
                        echo "\n*** started new process with PID ".$p->getPid()."\n";
                        echo $cmd."\n";
                    }
                    sleep(3); //don't start all processes at the same time
                }

                if ($this->_getParam('debug')) {
                    echo "Newletter $newsletterRow->id: currently sending with ".
                        round($newsletterRow->getCurrentSpeed()).
                        " mails/min\n";
                }
            }

            Kwf_Model_Abstract::clearAllRows();
            if ($this->_getParam('debug')) echo "sleep 10 secs.\n";
            sleep(10);
        }
    }

    public function sendAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        Kwf_Component_ModelObserver::getInstance()->disable();

        $newsletterId = $this->_getParam('newsletterId');
        $nlRow = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Model')->getRow($newsletterId);

        $mailsPerMinute = $nlRow->getCountOfMailsPerMinute();

        // In Schleife senden
        $queueLogModel = $nlRow->getModel()->getDependentModel('QueueLog');
        $count = 0; $countErrors = 0; $countNoUser = 0;
        $start = microtime(true);
        do {
            // Schlafen bis errechnet Zeit
            if ($nlRow->mails_per_minute != 'unlimited') {
                $sleep = $start + 60/$mailsPerMinute * $count - microtime(true);
                if ($sleep > 0) usleep($sleep * 1000000);
                if ($this->_getParam('debug')) {
                    echo "sleeping {$sleep}s\n";
                }
            }

            $nlStatus = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Model')->fetchColumnByPrimaryId('status', $nlRow->id);
            if ($nlStatus != 'sending') {
                if ($this->_getParam('debug')) {
                    echo "break sending because newsletter status changed to '$nlStatus'\n";
                }
                break;
            }


            Kwf_Benchmark::enable();
            Kwf_Benchmark::reset();
            Kwf_Benchmark::checkpoint('start');
            $userStart = microtime(true);

            // Zeile aus queue holen, falls nichts gefunden, Newsletter fertig
            $row = $nlRow->getNextQueueRow(getmypid());
            Kwf_Benchmark::checkpoint('get next recipient');
            if ($row) {

                $recipient = $row->getRecipient();
                if (!$recipient || !$recipient->getMailEmail()) {
                    $countNoUser++;
                    $status = 'usernotfound';
                } else if ($recipient instanceof Kwc_Mail_Recipient_UnsubscribableInterface &&
                    $recipient->getMailUnsubscribe())
                {
                    $countNoUser++;
                    $status = 'usernotfound';
                } else if ($recipient instanceof Kwf_Model_Row_Abstract &&
                    $recipient->hasColumn('activated') && !$recipient->activated)
                {
                    $countNoUser++;
                    $status = 'usernotfound';
                } else {
                    try {

                        $mc = $nlRow->getMailComponent();
                        $t = microtime(true);
                        $mail = $mc->createMail($recipient);
                        $createTime = microtime(true)-$t;

                        $t = microtime(true);
                        $mail->send();
                        $sendTime = microtime(true)-$t;
                        Kwf_Benchmark::checkpoint('send mail');

                        $count++;
                        $status = 'sent';
                    } catch (Exception $e) {
                        echo 'Exception in Sending Newsletter with id ' . $nlRow->id . ' with recipient ' . $recipient->getMailEmail();
                        echo $e->__toString();
                        $countErrors++;
                        $status = 'failed';
                    }
                    $nlRow->getModel()->getTable()->update(array(
                        'count_sent' => new Zend_Db_Expr('count_sent + 1'),
                        'last_sent_date' => date('Y-m-d H:i:s')
                    ), 'id = '.$nlRow->id);
                }

                $queueLogModel->createRow(array(
                    'newsletter_id' => $row->newsletter_id,
                    'recipient_model' => $row->recipient_model,
                    'recipient_id' => $row->recipient_id,
                    'status' => $status,
                    'send_date' => date('Y-m-d H:i:s')
                ))->save();

                $row->delete();

                Kwf_Benchmark::checkpoint('update queue');

                if ($this->_getParam('verbose')) {
                    if (Kwf_Benchmark::isEnabled() && $this->_getParam('benchmark')) {
                        echo Kwf_Benchmark::getCheckpointOutput();
                    }
                    echo "[".getmypid()."] $status in ".round((microtime(true)-$userStart)*1000)."ms (";
                    echo "create ".round($createTime*1000)."ms, ";
                    echo "send ".round($sendTime*1000)."ms";
                    echo ") [".round(memory_get_usage()/(1024*1024))."MB] [".round($count/(microtime(true)-$start), 1)." mails/s]\n";
                }

                if ($status == 'failed' && $this->_getParam('debug')) {
                    echo "stopping because sending failed in debug mode\n";
                    break;
                }

                if (memory_get_usage() > 100*1024*1024) {
                    if ($this->_getParam('debug')) {
                        echo "stopping because of >100MB memory usage\n";
                    }
                    break;
                }
            }

        } while ($row);
        $stop = microtime(true);

        // Log schreiben
        $logModel = $nlRow->getModel()->getDependentModel('Log');
        $row = $logModel->createRow(array(
            'newsletter_id' => $nlRow->id,
            'start' => date('Y-m-d H:i:s', floor($start)),
            'stop' => date('Y-m-d H:i:s', floor($stop)),
            'count' => $count,
            'countErrors' => $countErrors
        ));
        $row->save();

        // Debugmeldungen
        if ($this->_getParam('debug')) {
            $average = round($count/($stop-$start)*60);
            $info = $nlRow->getInfo();
            echo "\n";
            echo "$count Newsletters sent ($average/minute), $countErrors errors, $countNoUser user not found.\n";
            echo $info['text'] . "\n";
        }

        Kwf_Component_ModelObserver::getInstance()->enable();
    }

}
