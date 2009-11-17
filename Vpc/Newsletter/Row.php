<?php
class Vpc_Newsletter_Row extends Vps_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->create_date;
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
        $count = 0; $countErrors = 0; $countNoUser = 0;
        $start = microtime(true);
        do {
            // Schlafen bis errechnet Zeit
            $sleep = $start + 60/$mailsPerMinute * $count - microtime(true);
            if ($sleep > 0) usleep($sleep * 1000000);

            // Zeile aus queue holen, falls nichts gefunden, Newsletter fertig
            $row = $this->getNextRow($this->id);
            if ($row) {
                $row->status = 'sending';
                $row->save();

                $recipient = $row->getRecipient();
                if (!$recipient) {
                    $row->status = 'userNotFound';
                    $countNoUser++;
                } else if (!$recipient->getMailEmail()) {
                    $row->status = 'noAddress';
                    $countNoUser++;
                } else {
                    $result = $this->_sendMail($recipient);
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

    protected function _sendMail($recipient)
    {
        return $this->getMailComponent()->send($recipient);
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

    public function getLastRow()
    {
        $model = $this->getModel()->getDependentModel('Queue');
        $select = $model->select()
            ->whereEquals('status', 'sent')
            ->whereEquals('newsletter_id', $this->id)
            ->order('id', 'DESC')
            ->limit(1);
        return $model->getRow($select);
    }

    public function getInfo()
    {
        $queue = $this->getModel()->getDependentModel('Queue');
        $select = $queue->select()->whereEquals('newsletter_id', $this->id);
        $lastRow = $this->getLastRow();
        $ret = array();
        $ret['total']    = $queue->countRows($select);
        $ret['sent']     = $queue->countRows($select->whereEquals('status', 'sent'));
        $ret['notFound'] = $queue->countRows($select->whereEquals('status', 'userNotFound'));
        $ret['errors']   = $queue->countRows($select->whereEquals('status', 'sendingError'));
        $ret['queued']   = $queue->countRows($select->whereEquals('status', 'queued'));
        $ret['lastSentDate'] = $lastRow ? strtotime($lastRow->sent_date) : null;

        $text = '';
        switch ($this->status) {
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

    public function getMailComponent()
    {
        $componentId = $this->component_id . '_' . $this->id . '-mail';
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible' => true))
            ->getComponent();
    }
}