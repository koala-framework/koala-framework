<?php
class Kwf_Exception_JsErrorHandler_TestController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwf_Exception_JsErrorHandler');
        $this->view->xtype = 'panel';
        $this->view->html = '';
        for($i=1;$i<=8;$i++) {
            $this->view->html .= "<a href=\"javascript:testError$i()\">testError$i</a><br/>";
        }
    }

    public function getErrorLogEntryAction()
    {
        $error = '';
        foreach (glob('log/error/'.date('Y-m-d').'/*.txt') as $f) {
            preg_match('#([0-9]{2})_([0-9]{2})_([0-9]{2})_#', $f, $m);
            $t = strtotime(date('Y-m-d').' '.$m[1].':'.$m[2].':'.$m[3]);
            if ($t >= $this->_getParam('start')) {
                $error = file_get_contents($f);
                if (strpos($error, 'Kwf_Exception_JavaScript')!==false) {
                    unlink($f);
                }
            }
        }
        echo $error;
        exit;
    }
    
    public function getTimeAction()
    {
        echo time();
        exit;
    }
}
