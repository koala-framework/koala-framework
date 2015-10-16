<?php
class Kwf_Controller_Action_Component_PreviewController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->config = array(
            'responsive' => Kwf_Config::getValue('kwc.responsive')
        );
        $this->view->xtype = 'kwf.component.preview';
        $this->view->initialUrl = null;
        if (preg_match('#^https?://#', $this->_getParam('url'))) {
            $this->view->initialUrl = $this->_getParam('url');
        }

        if (!$this->view->initialUrl) {
            $https = Kwf_Util_Https::domainSupportsHttps($_SERVER['HTTP_HOST']);
            $protocol = $https ? 'https://' : 'http://';
            $this->view->initialUrl = $protocol.$_SERVER['HTTP_HOST'].Kwf_Setup::getBaseUrl().'/';
        }
    }

    public function redirectAction()
    {
        Kwf_Util_Redirect::redirect($this->_getParam('url'));
    }
}
