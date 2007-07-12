<?php
class Vps_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_Abstract
{
    var $view = null;
    var $_noRender = false;

    public function __construct(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        return $this;
    }

    public function setNoRender($noRender = true)
    {
        $this->_noRender = $noRender;
    }

    public function preDispatch() {
        $module = $this->getRequest()->getParam('module');
        if ($this->isJson()) {
            $this->view = new Vps_View_Json();
        } else {
            $this->view = new Vps_View_Smarty();
            if ($module == 'admin' || $module == 'component' || $module == 'componentedit') {
                $this->view->setScriptPath(VPS_PATH . 'views');
                $this->view->setCompilePath(VPS_PATH . 'views_c');
            }
        }

        if ((null !== $this->_actionController) && (null === $this->_actionController->view)) {
            $this->_actionController->view = $this->view;

            if ($module == 'component') {
                $id = $this->getRequest()->getParam('id');
                $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $id)->findComponent($id);
                if (!$component) {
                    throw new Vpc_Exception('Component not found.');
                } else {
                    $this->_actionController->component = $component;
                }
            }

        }

    }

    public function postDispatch()
    {
        if (!$this->_noRender
            && (null !== $this->_actionController)
            && $this->getRequest()->isDispatched()
            && !$this->getResponse()->isRedirect())
        {
            if ($this->isJson()) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                    $body = $this->view->render('');
                } else {
                    echo '<pre>';
                    print_r($this->view->getOutput());
                    echo '</pre>';
                    die();
                }
                $this->getResponse()->setHeader('Content-Type', 'text/javascript');
                $this->getResponse()->setBody($body);
            } else {
                $session = new Zend_Session_Namespace('admin');
                if ($session->mode == 'fe' || $this->getRequest()->getParam('fe')) {
                    $this->view->ext('Vps.FrontendEditing.Index');
                    $this->view->mode = 'fe';
                }
                $this->getResponse()->appendBody($this->view->render(''));
            }
        }
    }

    public function isJson()
    {
        $prefix = substr($this->getRequest()->getActionName(), 0, 4);
        return ($prefix == 'ajax' || $prefix == 'json');
    }
}
