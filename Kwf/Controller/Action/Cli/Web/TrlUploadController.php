<?php
class Kwf_Controller_Action_Cli_Web_TrlUploadController extends Kwf_Controller_Action_Cli_Abstract
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
        if (!is_file('trl.xml')) {
            throw new Kwf_Exception('trl.xml not existing');
        }

        $url = 'http://'.$trlConfig->web->path.'/api/import';

        $client = new Zend_Http_Client();
        $client->setAuth($trlConfig->web->user, $trlConfig->web->password);
        $client->setUri($url);
        $client->setParameterGet('projectId', $trlConfig->web->projectId);
        $client->setParameterGet('type', 'kwf');
        $client->setParameterGet('webCodeLanguage', Kwf_Registry::get('config')->webCodeLanguage);
        $client->setRawData(file_get_contents('trl.xml'), 'text/xml');
        $response = $client->request('POST');
        d($response->getBody());

        $this->_helper->viewRenderer->setNoRender(true);
    }
}
