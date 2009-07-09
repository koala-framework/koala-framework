<?php
class Vpc_Newsletter_Queue
{
    protected $_model = 'Vpc_Newsletter_QueueModel';
    protected $_logModel = 'Vpc_Newsletter_QueueLogModel';
    protected $_nlModel = 'Vpc_Newsletter_Model';
    private $_nlRow;

    public function send($timeLimit = 60, $mailsPerMinute = 20, $debugOutput = false)
    {
        if ($timeLimit) {
            set_time_limit($timeLimit + 10);
        } else {
            set_time_limit(0);
        }

        // Newsletter-ID rausfinden, die den Eintrag in der Queue mit der
        // kleinsten ID hat, von der wird dann gesendet
        $nlModel = Vps_Model_Abstract::getInstance($this->_nlModel);
        $select = $nlModel->select()
            ->where(new Vps_Model_Select_Expr_Or(array(
                new Vps_Model_Select_Expr_Equals('status', 'start'),
                new Vps_Model_Select_Expr_Equals('status', 'sending')
            )));
        $nlRow = null;
        $id = 0;
        foreach ($nlModel->getRows($select) as $r) {
            $row = $this->_getNextRow($r->id);
            // Wenn Newsletter auf "sending" ist, aber seit mehr als 5 Minuten
            // nichts mehr gesendet wurde, auf "start" stellen
            if ($r->status == 'sending') {
                $lastRow = $this->_getLastRow($r->id, 'Vpc_Newsletter_QueueModel');
                if ($lastRow && time() - strtotime($lastRow->sent_date) > 5*60) {
                    $r->status = 'start';
                    $r->save();
                }
            }
            if ($row && ($id == 0 || $row->id < $id) && $r->status=='start') {
                $nlRow = $r;
                $id = $row->id;
            }
        }

        if (!$nlRow) {
            if ($debugOutput) {
                echo "Nothing to send.\n";
            }
            return;
        }

        // Newsletter senden initialisieren
        $nlRow->status = 'sending';
        $nlRow->save();
        if ($debugOutput) {
            $timeLimitText = $timeLimit ? "for $timeLimit seconds" : "";
            echo "Sending newsletters of newletterId {$nlRow->id} $timeLimitText at a speed of $mailsPerMinute mails/minute\n";
        }

        // In Schleife senden
        $count = 0; $countErrors = 0; $countNoUser = 0;
        $start = microtime(true);
        do {
            // Schlafen bis errechnet Zeit
            $sleep = $start + 60/$mailsPerMinute * $count - microtime(true);
            if ($sleep > 0) usleep($sleep * 1000000);

            // Zeile aus queue holen, falls nichts gefunden, Newsletter fertig
            $row = $this->_getNextRow($nlRow->id);
            if ($row) {
                $row->status = 'sending';
                $row->save();

                $recipient = $this->getRecipient($row);
                if (!$recipient) {
                    $row->status = 'userNotFound';
                    $countNoUser++;
                } else {
                    $result = $this->sendMail($nlRow, $recipient);
                    if ($result) {
                        $row->status = 'sent';
                        $count++;
                        if ($debugOutput) echo '.';
                    } else {
                        $row->status = 'sendingError';
                        $countErrors++;
                        if ($debugOutput) echo 'x';
                    }
                }
                $row->sent_date = date('Y-m-d H:i:s');
                $row->save();
            } else {
                $nlRow->status = 'finished';
                $nlRow->save();
            }
        } while ($row && (((microtime(true) - $start) < ($timeLimit)) || !$timeLimit));
        $stop = microtime(true);

        if ($nlRow->status == 'sending') {
            $nlRow->status = 'start';
            $nlRow->save();
        }

        // Log schreiben
        $logModel = Vps_Model_Abstract::getInstance($this->_logModel);
        $row = $logModel->createRow(array(
            'newsletter_id' => $nlRow->id,
            'start' => date('Y-m-d H:i:s', floor($start)),
            'stop' => date('Y-m-d H:i:s', floor($stop)),
            'count' => $count,
            'countErrors' => $countErrors
        ));
        $row->save();

        // Debugmeldungen
        if ($debugOutput) {
            $average = floor($count/($stop-$start)*60);
            echo "\n";
            echo "$count Newsletters sent ($average/minute), $countErrors errors, $countNoUser user not found.\n";
            echo $this->getInfo() . "\n";
            if ($nlRow->status == 'finished') echo "Newsletter finished.\n";
        }
    }

    protected function sendMail($nlRow, $recipient)
    {
        $mail = $this->getMailComponent($nlRow);
        return $mail->send($recipient);
    }

    private function _getNextRow($newsletterId)
    {
        $model = Vps_Model_Abstract::getInstance($this->_model);
        $select = $model->select()
            ->whereEquals('status', 'queued')
            ->whereEquals('newsletter_id', $newsletterId)
            ->order('id')
            ->limit(1);
        return $model->getRow($select);
    }

    private static function _getLastRow($newsletterId, $model)
    {
        $model = Vps_Model_Abstract::getInstance($model);
        $select = $model->select()
            ->whereEquals('status', 'sent')
            ->whereEquals('newsletter_id', $newsletterId)
            ->order('id', 'DESC')
            ->limit(1);
        return $model->getRow($select);
    }

    public function getInfo($newsletterRow)
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
        $select = $model->select()->whereEquals('newsletter_id', $newsletterRow->id);
        $lastRow = self::_getLastRow($newsletterRow->id, 'Vpc_Newsletter_QueueModel');
        $ret = array();
        $ret['total']    = $model->countRows($select);
        $ret['sent']     = $model->countRows($select->whereEquals('status', 'sent'));
        $ret['notFound'] = $model->countRows($select->whereEquals('status', 'userNotFound'));
        $ret['errors']   = $model->countRows($select->whereEquals('status', 'sendingError'));
        $ret['queued']   = $model->countRows($select->whereEquals('status', 'queued'));
        $ret['lastSentDate'] = $lastRow ? strtotime($lastRow->sent_date) : null;

        $text = '';
        switch ($newsletterRow->status) {
            case 'stop': $text = trlVps('Newsletter stopped, cannot start again.'); break;
            case 'pause': $text = trlVps('Newsletter paused.'); break;
            case 'start': case 'sending': $text = trlVps('Newsletter sending.'); break;
            case 'finished': $text = trlVps('Newsletter finished.'); break;
            default: $text = trlVps('Newsletter waiting for start.'); break;
        }
        $ret['shortText'] = $text;
        $text .= ' ';

        $text .= trlVps(
            '{0} total, {1} sent, {2} waiting to send.',
            array($ret['total'], $ret['sent'], $ret['queued'])
        );
        if ($ret['notFound'] > 0) {
            $text .= ' ' . trlpVps('{0} receiver not found.', '{0} receivers not found.', $ret['notFound']);
        }
        if ($ret['errors'] > 0) {
            $text .= ' ' . trlVps('{0} errors while sending mail.', $ret['error']);
        }
        if ($ret['lastSentDate']) {
            $time = date(trlVps('Y-m-d H:i'), $ret['lastSentDate']);
            $t = ' ' . trlVps('Last mail sent: {0}', $time);;
            $text .= $t;
            $ret['shortText'] .= $t;
        }
        $ret['text'] = $text;

        return $ret;
    }

    public static function getRecipient($row)
    {
        $recipientModel = Vps_Model_Abstract::getInstance($row->recipient_model);
        return $recipientModel->getRow($row->recipient_id);
    }

    public static function getMailComponent($nlRow)
    {
        $componentId = $nlRow->component_id . '_' . $nlRow->id . '-mail';
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible' => true))
            ->getComponent();
    }
}
