<?php
class E3_Controller_Action_Fe extends E3_Controller_Action_Web
{
    public function saveAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $ret = $component->saveFrontendEditing($this->getRequest());
            if (!isset($ret['html'])) {
                $ret['html'] = $this->_renderPage($component, 'fe', true);
            }

            $body = Zend_Json::encode($ret);
            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->appendBody($body);
        }
    }

    public function cancelAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $ret = array();
            $ret['html'] = $this->_renderPage($component, 'fe', true);

            $body = Zend_Json::encode($ret);
            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->appendBody($body);
        }
    }
    
    public function editAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $body = $this->_renderPage($component, 'edit', true);
            $this->getResponse()
                ->setHeader('Content-Type', 'text/html')
                ->appendBody($body);
        }
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
        $id = $this->getRequest()->getQuery('componentId');
        if (is_null($id)) return null;
        $dao = Zend_Registry::get('dao');
        $className = str_replace(".", "_", $this->getRequest()->getQuery('componentClass'));
        $parts = E3_Component_Abstract::parseId($id);
        $component = new $className($dao, $parts['componentId'], $parts['pageKey'], $parts['componentKey']);
        return $component;
    }
}
?>
