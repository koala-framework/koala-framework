<?php
class Kwc_Newsletter_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_newsletter';
    protected $_rowClass = 'Kwc_Newsletter_Row';
    protected $_dependentModels = array(
        'Queue' => 'Kwc_Newsletter_QueueModel',
        'QueueLog' => 'Kwc_Newsletter_QueueLogModel',
        'Log' => 'Kwc_Newsletter_LogModel',
        'Mail' => 'Kwc_Mail_Model'
    );

    public function send($timeLimit = 60, $mailsPerMinute = 20, $debugOutput = false)
    {
        // select random newsletter to send
        $select = $this->select()
            ->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('status', 'start'),
                new Kwf_Model_Select_Expr_Equal('status', 'sending')
            )))
            ->order(Kwf_Model_Select::ORDER_RAND);
        $nlRow = null;
        $id = 0;
        foreach ($this->getRows($select) as $r) {
            $row = $r->getNextRow($r->id);
            // Wenn Newsletter auf "sending" ist, aber seit mehr als 5 Minuten
            // nichts mehr gesendet wurde, auf "start" stellen
            if ($r->status == 'sending' && time() - strtotime($r->last_sent_date) > 5*60) {
                $r->status = 'start';
                $r->save();
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

        $nlRow->send($timeLimit, $mailsPerMinute, $debugOutput);
    }
}