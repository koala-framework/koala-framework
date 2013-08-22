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

    public final function send($debugOutput = false)
    {
        throw new Kwf_Exception("moved to cli controller");
    }

    protected final function _sendMail($recipient, $debugOutput = false)
    {
        throw new Kwf_Exception("moved to cli controller");
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
        $ret['speed'] = $this->mails_per_minute;

        $queueLogModel = $this->getModel()->getDependentModel('QueueLog');
        $select = new Kwf_Model_Select();

        $seconds = ($ret['queued'] / $this->getCountOfMailsPerMinute()) * 60;
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        $ret['remainingTime'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

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

    public function getCountOfMailsPerMinute()
    {
        $mailsPerMinute = 30;
        if ($this->mails_per_minute == 'fast') {
            $mailsPerMinute = 100;
        } else if ($this->mails_per_minute == 'normal') {
            $mailsPerMinute = 40;
        } else if ($this->mails_per_minute == 'slow') {
            $mailsPerMinute = 20;
        }
        return $mailsPerMinute;
    }
}
