<?php
class Vps_Component_Generator_Plugin_StatusUpdate_SendFormController extends Vps_Controller_Action
{
    private function _getBackend($backend)
    {
        $callbackUrl = 'http://';
        if (isset($_SERVER['HTTP_HOST'])) {
            $callbackUrl .= $_SERVER['HTTP_HOST'];
        } else {
            $callbackUrl .= Vps_Registry::get('config')->server->domain;
        }
        $callbackUrl .= Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Auth');
        $callbackUrl .= '/callback?componentId='.$this->_getParam('componentId');
        $callbackUrl .= '&backend='.$backend;

        $backends = Vpc_Abstract::getSetting($this->_getParam('class'), 'backends');
        $backend = $backends[$backend];
        return new $backend($callbackUrl);
    }

    public function preDispatch()
    {
        $this->_form = new Vps_Form();
        $this->_form->setModel(new Vps_Model_FnF());
        $this->_form->setCreateMissingRow(true);
        parent::preDispatch();
    }

    public function jsonDefaultTextAction()
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
        $url = 'http://'.Vps_Registry::get('config')->server->domain.$c->url;
        $short = file_get_contents('http://api.bit.ly/v3/shorten?login=vividplanet&apiKey=R_b8c9ca5855b412b8d81ef3fff9985e0c&longUrl='.rawurlencode($url).'&format=json');
        $short = Zend_Json::decode($short);
        if ($short['status_code'] != 200) {
            throw new Vps_Exception("getting short url failed");
        }
        $this->view->text = $c->name.': '.$short['data']['url'];
    }

    public function jsonSendAction()
    {
        $services = explode(',', $this->_getParam('services'));
        foreach ($services as $service) {
            $t = $this->_getBackend($service);
            if (!$t->isAuthed()) {
                $url = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Auth');
                $this->view->requestAuthUrl = $url.'/request?componentId='.$this->_getParam('componentId').'&backend=twitter';
                $this->view->backendName = $t->getName();
            } else {
                $logRow = Vps_Model_Abstract::getInstance('Vps_Component_Generator_Plugin_StatusUpdate_LogModel')
                    ->createRow();
                $logRow->type = $service;
                $logRow->component_id = $this->_getParam('componentId');
                $logRow->date = date('Y-m-d H:i:s');
                $logRow->message = $this->_getParam('text');
                $t->send($logRow->message, $logRow);
                $logRow->save();
            }
        }
    }
}
