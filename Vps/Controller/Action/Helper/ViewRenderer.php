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
            if ($module == 'admin' || $module == 'component') {
                $this->view->setScriptPath(VPS_PATH . '/views');
                $this->view->setCompilePath(VPS_PATH . '/views_c');
            }
        }

        if ((null !== $this->_actionController) && (null === $this->_actionController->view)) {
            $this->_actionController->view = $this->view;

            if ($module == 'component') {
                $id = $this->getRequest()->getParam('componentId');
                $pageCollection = Vps_PageCollection_TreeBase::getInstance();
                $component = $pageCollection->findComponent($id);
                if (!$component) {
                    $class = $this->getRequest()->getParam('class');
                    Zend_Loader::loadClass($class);
                    $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $class, $id);
                }
                if (!$component) {
                    throw new Vpc_Exception('Component not found: ' . $id);
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
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->getResponse()->setHeader('Content-Type', 'text/html');
                    $this->getResponse()->setBody($this->view->render(''));
                } else if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                    $this->getResponse()->setHeader('Content-Type', 'text/javascript');
                    $this->getResponse()->setBody($this->view->render(''));
                } else {
                    echo '<pre>';
                    print_r($this->view->getOutput());
                    echo '</pre>';
                    die();
                }
            } else {
                $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
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
