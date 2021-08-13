<?php
class Kwf_Controller_Action_Form_Controller extends Kwf_Controller_Action
{
    protected $_form;

    public function preDispatch()
    {
        if ($this->getRequest()->getActionName() == 'success') {
            if (!$this->getRequest()->getParam('row') instanceof Kwf_Model_Row_Interface) {
                //don't allow call to successAction directly, allow only forward from indexAction
                throw new Kwf_Exception_AccessDenied();
            }
        }
        $this->view->dep = Kwf_Assets_Package_Default::getInstance('Admin');

        parent::preDispatch();
    }

    protected function _initFields()
    {
    }

    public function indexAction()
    {
        if (is_string($this->_form)) $this->_form = new $this->_form('form');
        if (!$this->_form && isset($this->_formName)) $this->_form = new $this->_formName('form');

        $this->_initFields();
        $this->_form->initFields();
        $this->_form->trlStaticExecute();

        if (!$this->_form->getModel() && $this->_model) {
            $this->_form->setModel(Kwf_Model_Abstract::getInstance($this->_model));
        }
        if (!$this->_form->getModel()) {
            throw new Kwf_Exception("No model set for form");
        }

        $this->_form->trlStaticExecute();




        $postData = $this->getRequest()->getParams();

        $errors = array();
        $postData = $this->_form->processInput(null, $postData);
        $this->getRequest()->setParam('formPostData', $postData);
        $this->getRequest()->setParam('row', $this->_form->getRow());

        if (isset($postData[$this->_form->getName()])) {

            $errors = $this->_form->validate(null, $postData);
            if (!$errors) {
                ignore_user_abort(true);
                try {
                    $this->_form->prepareSave(null, $postData);
                    $this->_form->save(null, $postData);
                    $this->_form->afterSave(null, $postData);
                    $this->_afterSave($this->_form->getRow());
                } catch (Kwf_Exception_Client $e) {
                    $errors[] = array(
                        'message' => $e->getMessage()
                    );
                }
            }
            if ($errors) {
                $this->getRequest()->setParam('formErrors', $errors);
            }
        }

        $this->_showForm();
    }

    protected function _afterSave($row)
    {
        $this->forward('success');
    }

    protected function _showForm()
    {
        $this->view->formName = $this->_form->getName();

        $this->view->method = 'post';
        $this->view->submitButtonText = trlKwf('Submit');
        $this->view->action = '';

        $values = $this->_form->load(null, $this->_getParam('formPostData'));
        $this->view->form = $this->_form->getTemplateVars($values, '', $this->view->formName.'_');

        $errors = $this->_getParam('formErrors');
        if (!$errors) $errors = array();
        $dec = new Kwc_Form_Decorator_Label();
        $this->view->form = $dec->processItem($this->view->form, $errors);
        $this->view->errors = Kwf_Form::formatValidationErrors($errors);
        $this->view->errorsHtml = '';
        if ($this->view->errors) {
            $this->view->errorsHtml .= '<div class="webStandard kwcFormError webFormError">';
            $this->view->errorsHtml .= '<p class="error">'.trlKwf('An error has occurred').':</p>';
            $this->view->errorsHtml .= '<ul>';
            foreach ($this->view->errors as $error) {
                $this->view->errorsHtml .= '<li>' . htmlspecialchars($error) . '</li>';
            }
            $this->view->errorsHtml .= '</ul>';
            $this->view->errorsHtml .= '</div>';
        }
    }

    public function successAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('success');
    }
}
