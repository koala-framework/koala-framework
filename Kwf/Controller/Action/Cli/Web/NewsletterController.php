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
        throw new Kwf_Exception_Client("Don't call Newsletter controller directly. Use process-control and maintenance-jobs instead.");
    }

    public function sendAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        Kwf_Events_ModelObserver::getInstance()->disable();

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

        Kwf_Events_ModelObserver::getInstance()->enable();
    }

}
