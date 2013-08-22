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
            do {
                $select = $model->select()
                    ->where(new Kwf_Model_Select_Expr_Or(array(
                        new Kwf_Model_Select_Expr_Equal('status', 'start'),
                        new Kwf_Model_Select_Expr_And(array(
                            new Kwf_Model_Select_Expr_Equal('status', 'startLater'),
                            new Kwf_Model_Select_Expr_HigherEqual('start_date', new Kwf_DateTime(time())),
                        )),
                        new Kwf_Model_Select_Expr_Equal('status', 'sending')
                    )))
                    ->order('RAND()');
                $rows = $model->getRows($select);
                $activeCountRows = count($rows);
                foreach ($rows as $newsletterRow) {
                    if ($newsletterRow->status == 'sending' && time() - strtotime($newsletterRow->last_sent_date) < 60) {
                        if ($this->_getParam('debug')) echo "still sending in other process ($newsletterRow->id)\n";
                        $activeCountRows--;
                        continue;
                    }
                    if (!isset($procs[$newsletterRow->id])) {
                        $procs[$newsletterRow->id] = array();
                    }

                    //remove stopped processes (might stop because of memory limit or simply crash for some reason)
                    foreach ($procs[$newsletterRow->id] as $k=>$p) {
                        if (!$p->isRunning()) {
                            echo "process ".$p->getPid()." stopped...\n";
                            unset($procs[$newsletterRow->id][$k]);
                        }
                    }

                    if ($this->_getParam('debug')) {
                        echo count($procs[$newsletterRow->id])." running processes\n";
                    }

                    $numOfProcesses = 1;
                    if ($newsletterRow->mails_per_minute == 'fast') {
                        $numOfProcesses = 5;
                    }
                    while (count($procs[$newsletterRow->id]) < $numOfProcesses) {
                        $cmd = "php bootstrap.php newsletter send --newsletterId=$newsletterRow->id";
                        if ($this->_getParam('debug')) $cmd .= " --debug";
                        if ($this->_getParam('debug')) {
                            echo "\n*** starting new process...\n";
                            echo $cmd."\n";
                        }
                        $descriptorspec = array(
                            1 => STDOUT,
                            2 => STDERR,
                        );
                        $p = new Kwf_Util_Proc($cmd, $descriptorspec);
                        $procs[$newsletterRow->id][] = $p;
                        echo "PID: ".$p->getPid()."\n";
                    }
                }

                if ($this->_getParam('debug')) echo "sleep 10 secs (I).\n";
                sleep(10);

                Kwf_Model_Abstract::clearAllRows();
            } while($activeCountRows);
            if ($this->_getParam('debug')) echo "sleep 10 secs (II).\n";
            sleep(10);
        }
    }

    public function sendAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $newsletterId = $this->_getParam('newsletterId');
        $nlRow = Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Model')->getRow($newsletterId);
        $nlRow->send($this->_getParam('debug'));
    }
}
