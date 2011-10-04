<?php
class Vpc_Newsletter_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_newsletter';
    protected $_rowClass = 'Vpc_Newsletter_Row';
    protected $_dependentModels = array(
        'Queue' => 'Vpc_Newsletter_QueueModel',
        'Log' => 'Vpc_Newsletter_LogModel',
        'Mail' => 'Vpc_Mail_Model'
    );

    public function send($timeLimit = 60, $mailsPerMinute = 20, $debugOutput = false)
    {
        // Newsletter-ID rausfinden, die den Eintrag in der Queue mit der
        // kleinsten ID hat, von der wird dann gesendet
        $select = $this->select()
            ->where(new Vps_Model_Select_Expr_Or(array(
                new Vps_Model_Select_Expr_Equal('status', 'start'),
                new Vps_Model_Select_Expr_Equal('status', 'sending')
            )));
        $nlRow = null;
        $id = 0;
        foreach ($this->getRows($select) as $r) {
            $row = $r->getNextRow($r->id);
            // Wenn Newsletter auf "sending" ist, aber seit mehr als 5 Minuten
            // nichts mehr gesendet wurde, auf "start" stellen
            if ($r->status == 'sending') {
                $lastRow = $r->getLastRow($r->id, 'Vpc_Newsletter_QueueModel');
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

        $nlRow->send($timeLimit, $mailsPerMinute, $debugOutput);
    }
}