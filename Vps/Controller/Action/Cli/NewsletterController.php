<?php
class Vps_Controller_Action_Cli_NewsletterController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "Call by cronjob to send waiting newsletters. If called manually, Ctrl+C stops newsletter, ist has to be started again!";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'timeLimit',
                'value'=> 60,
                'valueOptional' => true,
            ),
            array(
                'param'=> 'mailsPerMinute',
                'value'=> 40,
                'valueOptional' => true,
            ),
            array(
                'param'=> 'debugOutput',
                'value'=> true,
                'valueOptional' => true,
            )
        );
    }

    public function indexAction()
    {
        $queue = new Vpc_Newsletter_Queue();
        $queue->send($this->_getParam('timeLimit'), $this->_getParam('mailsPerMinute'), $this->_getParam('debugOutput'));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
