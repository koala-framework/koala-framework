<?php
class Kwf_Controller_Action_Cli_Web_TrlDownloadController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "parse for translation calls";
    }

    public function indexAction()
    {
        $trlConfig = Kwf_Registry::get('config')->translation;
        if (!isset($trlConfig->web)) {
            throw new Kwf_Exception('Only translation-web supported');
        }

        $url = 'http://'.$trlConfig->web->user.':'.$trlConfig->web->password.'@'
                .$trlConfig->web->path.'/api/export'.'?projectId='.$trlConfig->web->projectId.'&type=kwf';
        if (copy($url, 'mytrl.xml')) {
            echo 'trl.xml successfully downloaded'."\n";
        } else {
            echo 'error downloading trl.xml'."\n";
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
