<?php
class Kwc_Newsletter_Row extends Kwf_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->subject;
    }

    public function __get($name)
    {
        if ($name == 'info_short') {
            $info = $this->getInfo();
            return $info['shortText'];
        } else if ($name == 'info') {
            $info = $this->getInfo();
            return $info['text'];
        } else if ($name == 'subject') {
            $model = $this->getModel()->getDependentModel('Mail');
            $id = $this->component_id . '_' . $this->id . '-mail';
            $mailRow = $model->getRow($id);
            if ($mailRow) return $mailRow->subject;
            return '';
        } else {
            return parent::__get($name);
        }
    }


    public function send($timeLimit = 60, $mailsPerMinute = 20, $debugOutput = false)
    {
        if ($timeLimit) {
            set_time_limit($timeLimit + 10);
        } else {
            set_time_limit(0);
        }

        // Newsletter senden initialisieren
        $this->status = 'sending';
        $this->save();
        if ($debugOutput) {
            $timeLimitText = $timeLimit ? "for $timeLimit seconds" : "";
            echo "Sending newsletters of newletterId {$this->id} $timeLimitText at a speed of $mailsPerMinute mails/minute\n";
        }

        // In Schleife senden
        $queueLogModel = $this->getModel()->getDependentModel('QueueLog');
        $count = 0; $countErrors = 0; $countNoUser = 0;
        $start = microtime(true);
        do {
            // Schlafen bis errechnet Zeit
            $sleep = $start + 60/$mailsPerMinute * $count - microtime(true);
            if ($sleep > 0) usleep($sleep * 1000000);
            if ($debugOutput) {
                //echo "sleeping {$sleep}s\n";
            }

            // Zeile aus queue holen, falls nichts gefunden, Newsletter fertig
            $row = $this->getNextRow($this->id);
            if ($row) {

                $row->status = 'sending';
                $row->save();

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
                        $this->_sendMail($recipient, $debugOutput);
                        $count++;
                        if ($debugOutput) echo '.';
                        $status = 'sent';
                    } catch (Exception $e) {
                        echo 'Exception in Sending Newsletter with id ' . $this->id . ' with recipient ' . $recipient->getMailEmail();
                        echo $e->__toString();
                        $countErrors++;
                        $status = 'failed';
                    }
                    $this->count_sent++;
                    $this->last_sent_date = date('Y-m-d H:i:s');
                    $this->save();
                }

                $queueLogModel->createRow(array(
                    'newsletter_id' => $row->newsletter_id,
                    'recipient_model' => $row->recipient_model,
                    'recipient_id' => $row->recipient_id,
                    'searchtext' => $row->searchtext,
                    'status' => $status,
                    'send_date' => date('Y-m-d H:i:s')
                ))->save();

                $row->delete();

            } else {

                $this->status = 'finished';
                $this->save();

            }

        } while ($row && (((microtime(true) - $start) < ($timeLimit)) || !$timeLimit));
        $stop = microtime(true);

        if ($this->status == 'sending') {
            $this->status = 'start';
            $this->save();
        }

        // Log schreiben
        $logModel = $this->getModel()->getDependentModel('Log');
        $row = $logModel->createRow(array(
            'newsletter_id' => $this->id,
            'start' => date('Y-m-d H:i:s', floor($start)),
            'stop' => date('Y-m-d H:i:s', floor($stop)),
            'count' => $count,
            'countErrors' => $countErrors
        ));
        $row->save();

        // Debugmeldungen
        if ($debugOutput) {
            $average = floor($count/($stop-$start)*60);
            $info = $this->getInfo();
            echo "\n";
            echo "$count Newsletters sent ($average/minute), $countErrors errors, $countNoUser user not found.\n";
            echo $info['text'] . "\n";
            if ($this->status == 'finished') echo "Newsletter finished.\n";
        }
    }

    protected function _sendMail($recipient, $debugOutput = false)
    {
        $mc = $this->getMailComponent();
        $t = microtime(true);
        $mail = $mc->createMail($recipient);
        //if ($debugOutput) echo "createMail: ".round((microtime(true)-$t)*1000)."ms\n";

        $t = microtime(true);
        $mail->send();
        //if ($debugOutput) echo "send: ".round((microtime(true)-$t)*1000)."ms\n";
    }

    public function getNextRow()
    {
        $model = $this->getModel()->getDependentModel('Queue');
        $select = $model->select()
            ->whereEquals('status', 'queued')
            ->whereEquals('newsletter_id', $this->id)
            ->order('id')
            ->limit(1);
        return $model->getRow($select);
    }

    public function getInfo()
    {
        $queue = $this->getModel()->getDependentModel('Queue');
        $select = $queue->select()->whereEquals('newsletter_id', $this->id);
        $ret = array();
        $ret['state']    = $this->status;
        $ret['sent']     = $this->count_sent;
        $ret['total']    = $queue->countRows($select) + $this->count_sent;
        $ret['queued']   = $queue->countRows($select->whereEquals('status', 'queued'));
        $ret['lastSentDate'] = strtotime($this->last_sent_date);

        $text = '';
        switch ($this->status) {
            case 'stop': $text = trlKwf('Newsletter stopped, cannot start again.'); break;
            case 'pause': $text = trlKwf('Newsletter paused.'); break;
            case 'start': case 'sending': $text = trlKwf('Newsletter sending.'); break;
            case 'finished': $text = trlKwf('Newsletter finished.'); break;
            default: $text = trlKwf('Newsletter waiting for start.'); break;
        }
        $ret['shortText'] = $text;
        $text .= ' ';

        $text .= trlKwf(
            '{0} sent, {1} waiting to send.',
            array($ret['sent'], $ret['queued'])
        );
        if ($ret['lastSentDate']) {
            $time = date(trlKwf('Y-m-d H:i'), $ret['lastSentDate']);
            $t = ' ' . trlKwf('Last mail sent: {0}', $time);;
            $text .= $t;
            $ret['shortText'] .= $t;
        }
        $ret['text'] = $text;

        return $ret;
    }

    public function getMailComponent()
    {
        $componentId = $this->component_id . '_' . $this->id . '-mail';
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible' => true))
            ->getComponent();
    }
}
