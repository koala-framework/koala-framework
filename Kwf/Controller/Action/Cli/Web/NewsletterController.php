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
        ini_set('memory_limit', '512M');
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_Newsletter_Component');
        if (empty($components)) return;
        $model = $components[0]->getComponent()->getChildModel();
        $model->send($this->_getParam('timeLimit'), $this->_getParam('debug'));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
