<?php
class Vpc_Form_FrontendFormController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');

    public function preDispatch()
    {
        $componentClass = $this->_getParam('class');
        $formClass = Vpc_Admin::getComponentClass($componentClass, 'FrontendForm');
        if ($formClass && $formClass != 'Vpc_Abstract_Form') {
            $this->_form = new $formClass($componentClass, $componentClass);
        }
        parent::preDispatch();
    }

    public function jsonSaveAction()
    {
        $postData = $this->getRequest()->getParams();
        if (!isset($postData['componentId'])) throw new Vps_Exception_Client('component not found');

        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentById($postData['componentId'], array('ignoreVisible' => true));
        if (!$component) throw new Vps_Exception_Client('component not found');
        $component = $component->getComponent();
        $component->processInput($postData);

        $errors = $component->getErrors();
        $this->view->errorPlaceholder = $component->getPlaceholder('error');
        $this->view->errorFields = array();
        $this->view->errorMessages = array();
        foreach ($errors as $error) {
            if (isset($error['message'])) {
                $error['messages'] = array($error['message']);
            }
            $msgs = array();
            foreach ($error['messages'] as $msg) {
                $msgs[] = htmlspecialchars($msg);
            }
            if (isset($error['field'])) {
                //if message is associated with a specific field show it there
                $this->view->errorFields[$error['field']->getFieldName()] = implode('<br />', $msgs);
            } else {
                //else just above the form
                $this->view->errorMessages = array_merge($this->view->errorMessages, $msgs);
            }
        }
        $this->view->successContent = null;
        if (!$this->view->errorMessages && !$this->view->errorFields) {
            $success = $component->getData()->getChildComponent('-success');
            if ($success) {
                $renderer = new Vps_Component_Renderer();
                $this->view->successContent = $renderer->renderComponent($success);
            }
        }
        $this->view->errorFields = (object)$this->view->errorFields;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }
}
