<?php
class Kwf_Controller_Action_Component_PreviewController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->config = array(
            'responsive' => Kwf_Config::getValue('kwc.responsive')
        );
        $this->view->xtype = 'kwf.component.preview';
        $url = null;
        if (preg_match('#^https?://#', $this->_getParam('url'))) {
            $url = $this->_getParam('url');
        }
        $url = filter_var($url, FILTER_VALIDATE_URL);
        if ($url) {
            $parsedUrl = parse_url($url);
            $data = Kwf_Component_Data_Root::getInstance()->getPageByUrl($parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/', '');
            if (!$data) $url = null;
        }
        $this->view->initialUrl = $url;

        if (!$this->view->initialUrl) {
            $https = Kwf_Util_Https::domainSupportsHttps($_SERVER['HTTP_HOST']);
            $protocol = $https ? 'https://' : 'http://';
            $this->view->initialUrl = $protocol.$_SERVER['HTTP_HOST'] . '/';
        }
    }

    public function redirectAction()
    {
        Kwf_Util_Redirect::redirect($this->_getParam('url'));
    }
}
