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
            ),
            array(
                'param'=> 'timeLimit',
                'value'=> 55,
                'valueOptional' => true,
            )
        );
    }

    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Model');

        // select random newsletter to send
        $select = $this->select()
            ->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('status', 'start'),
                new Kwf_Model_Select_Expr_Equal('status', 'startLater'),
                new Kwf_Model_Select_Expr_Equal('status', 'sending')
            )))
            ->order('RAND()');
        $nlRow = null;
        $id = 0;
        foreach ($this->getRows($select) as $r) {
            $row = $r->getNextRow($r->id);

            if ($r->status == 'startLater' && time()>=strtotime($r->start_date)) {
                $r->status = 'start';
                $r->save();
            }
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
            if ($this->_getParam('debug')) {
                echo "Nothing to send.\n";
            }
            return;
        }

        $nlRow->send($this->_getParam('timeLimit'), $this->_getParam('debug'));
    }
}
