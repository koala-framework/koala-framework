<?php
class Vps_Controller_Action_Fe extends Vps_Controller_Action_Web
{
    public function saveAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $ret = $component->saveFrontendEditing($this->getRequest());
            if (!isset($ret['html'])) {
                $ret['html'] = $this->_renderPage($component, 'fe', true);
            }
            $pageCollection = Vps_PageCollection_Abstract::getInstance();
            $page = $pageCollection->getPageById($this->getRequest()->getParam('currentPageId'));
            $ret['createComponents'] = $page->getComponentInfo();
            foreach ($ret['createComponents'] as $key => $component) {
                $filename = str_replace('_', '/', $component) . '.js';
                if (!is_file('../library/' . $filename)) {
                    unset($ret['createComponents'][$key]);
                }
            }
            $ret['success'] = true;
            $body = Zend_Json::encode($ret);
            $this->getResponse()->appendBody($body);
        }
    }

    public function cancelAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $ret = array();
            $ret['html'] = $this->_renderPage($component, 'fe', true);
            $pageCollection = Vps_PageCollection_Abstract::getInstance();
            $page = $pageCollection->getPageById($this->getRequest()->getParam('currentPageId'));
            $ret['createComponents'] = $page->getComponentInfo();
            foreach ($ret['createComponents'] as $key => $component) {
                $filename = str_replace('_', '/', $component) . '.js';
                if (!is_file('../library/' . $filename)) {
                    unset($ret['createComponents'][$key]);
                }
            }

            $body = Zend_Json::encode($ret);
            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->appendBody($body);
        }
    }
    
    public function editAction()
    {
        $component = $this->_createComponent();
        $data = $component->getFrontendEditingData();
        $data['success'] = true;
        $this->getResponse()->setBody(Zend_Json::encode($data));
        /*
        if (!is_null($component)) {
            $body = $this->_renderPage($component, 'edit', true);
            $this->getResponse()
                ->setHeader('Content-Type', 'text/html')
                ->appendBody($body);
        }
        */
    }

    public function statusAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $body = Zend_Json::encode($component->getStatus());
            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->appendBody($body);
        }
    }

    private function _createComponent()
    {
        $id = $this->getRequest()->getParam('componentId');
        if (is_null($id)) return null;
        $dao = Zend_Registry::get('dao');
        $className = str_replace(".", "_", $this->getRequest()->getParam('componentClass'));
        $parts = Vps_Component_Abstract::parseId($id);
        $component = new $className($dao, $parts['componentId'], $parts['pageKey'], $parts['componentKey']);
        return $component;
    }
}
?>
