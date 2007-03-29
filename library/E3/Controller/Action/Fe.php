<?php
class E3_Controller_Action_Fe extends E3_Controller_Action_Web
{
    public function saveAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $component->saveFrontendEditing();
            $this->_renderPage($component, 'fe', true);
        }
    }
    
    public function cancelAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $this->_renderPage($component, 'fe', true);
        }
    }
    
    public function editAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $this->_renderPage($component, 'edit', true);
        }
    }

    private function _createComponent()
    {
        $id = $this->getRequest()->getQuery('componentId');
        if (is_null($id)) return null;
        $dao = Zend_Registry::get('dao');
        $className = str_replace(".", "_", $this->getRequest()->getQuery('componentClass'));
        preg_match('#^([^_\\-]*)_?([^_\\-]*)\\-?([^_\\-]*)$#', $id, $keys);
        $component = new $className($dao, $keys[1], $keys[2], $keys[3]);
        return $component;
    }
}
?>
