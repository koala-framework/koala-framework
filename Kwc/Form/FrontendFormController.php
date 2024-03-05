<?php
class Kwc_Form_FrontendFormController extends Kwf_Controller_Action
{
    public function jsonSaveAction()
    {
        $postData = $this->getRequest()->getParams();
        if (!isset($postData['componentId'])) throw new Kwf_Exception_Client('component not found');

        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById((string)$postData['componentId'], array('ignoreVisible' => true));
        if (!$component) throw new Kwf_Exception_Client('component not found');
        $component = $component->getComponent();
        $component->processAjaxInput($postData);

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
                $msgs[] = Kwf_Util_HtmlSpecialChars::filter($msg);
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
            $success = $component->getData()->getComponent()->getSuccessComponent();
            if ($success instanceof Kwf_Component_Data) {
                if ($success->isPage) {
                    $this->view->successUrl = $success->url;
                } else {
                    $process = $success
                        ->getRecursiveChildComponents(array(
                                'page' => false,
                                'flags' => array('processInput' => true)
                            ));
                    if (Kwf_Component_Abstract::getFlag($success->componentClass, 'processInput')) {
                        $process[] = $success;
                    }
                    $postData = array(); //empty because there can't be anything as we didn't display the success yet
                    foreach ($process as $i) {
                        if (method_exists($i->getComponent(), 'preProcessInput')) {
                            $i->getComponent()->preProcessInput($postData);
                        }
                    }
                    foreach ($process as $i) {
                        if (method_exists($i->getComponent(), 'processInput')) {
                            $i->getComponent()->processInput($postData);
                        }
                    }
                    if (class_exists('Kwf_Events_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
                        Kwf_Events_ModelObserver::getInstance()->process(false);
                    }
                    $renderer = new Kwf_Component_Renderer();
                    $this->view->successContent = $renderer->renderComponent($success);
                }
            } else if (is_string($success)) {
                $this->view->successUrl = $success;
            }
        }
        $this->view->errorFields = (object)$this->view->errorFields;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }

    protected function _getAuthData()
    {
        return null;
    }

    protected function _getUserRole()
    {
        return 'guest';
    }
}
