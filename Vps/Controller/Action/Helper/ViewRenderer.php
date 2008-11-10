<?php
class Vps_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
    public function init()
    {
        $this->setNoController();
        $this->setViewSuffix('tpl');
        $this->setRender('master');
    }
    
    public function preDispatch() {
        $module = $this->getRequest()->getParam('module');
        if ($this->isJson()) {
            $this->setView(new Vps_View_Json());
        } else {
            $this->setView(new Vps_View_Ext());
        }

        if ((null !== $this->_actionController) && (null === $this->_actionController->view)) {
            $this->_actionController->view = $this->view;
        }
        parent::preDispatch();
    }

    public function postDispatch()
    {
        if (!$this->_noRender
            && (null !== $this->_actionController)
            && $this->getRequest()->isDispatched()
            && !$this->getResponse()->isRedirect()) {

            if ($this->isJson()) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->getResponse()->setHeader('Content-Type', 'text/html');
                } else if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                    $this->getResponse()->setHeader('Content-Type', 'text/javascript');
                } else {
                    echo '<pre>';
                    print_r($this->view->getOutput());
                    echo '</pre>';
                    $this->setNoRender();
                }
            } else {
                $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
            }
        }
        parent::postDispatch();
    }

    public function isJson()
    {
        return substr($this->getRequest()->getActionName(), 0, 4) == 'json';
    }
}
