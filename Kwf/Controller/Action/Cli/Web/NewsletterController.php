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
                    $cmd = "php bootstrap.php newsletter send --newsletterId=$newsletterRow->id";
                    if ($this->_getParam('debug')) $cmd .= " --debug";
                    if ($this->_getParam('debug')) echo $cmd."\n";
                    passthru($cmd);
                }

                Kwf_Model_Abstract::clearAllRows();
            } while($activeCountRows);
            if ($this->_getParam('debug')) echo "sleep 10 secs.\n";
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
