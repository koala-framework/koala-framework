<?php
class Kwc_Form_Component extends Kwc_Abstract_Composite_Component
{
    protected $_form;
    private $_initialized = false;
    private $_formTrlStaticExecuted = false;
    protected $_errors = array();

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['success'] = 'Kwc_Form_Success_Component';
        $ret['generators']['child']['component']['header'] = null;
        $ret['generators']['child']['component']['footer'] = null;
        $ret['componentName'] = trlKwfStatic('Form');
        $ret['placeholder']['submitButton'] = trlKwfStatic('Submit');
        $ret['placeholder']['error'] = trlKwfStatic('An error has occurred');
        $ret['decorator'] = 'Kwc_Form_Decorator_Label';
        $ret['viewCache'] = true;

        $ret['hideFormOnSuccess'] = true;

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        $ret['errorStyle'] = null; //default from config.ini: kwc.form.errorStyle

        $ret['rootElementClass'] = 'default';

        return $ret;
    }

    public function getSubmitDataLayer()
    {
        return array();
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);

        // wenn es eine Form.php gibt aber keine FrontendForm.php
        // sollte man aus irgendeinem grund doch eine Form benutzen ohne FrontendForm
        // dann einfach validateSettings überschreiben und parent nicht aufrufen
        $frontendFormClass = Kwc_Admin::getComponentClass($componentClass, 'FrontendForm');
        $formClass = Kwc_Admin::getComponentClass($componentClass, 'Form');
        if ($formClass != 'Kwc_Abstract_Composite_Form' && !$frontendFormClass) {
            throw new Kwf_Exception("Form.php files for frontend have been renamed to FrontendForm.php");
        }

        if ($frontendFormClass && is_instance_of($frontendFormClass, 'Kwc_Abstract_Form')) {
            throw new Kwf_Exception("A frontend form may never be an instance of Kwc_Abstract_Form");
        }
    }

    protected function _initForm()
    {
        if (!isset($this->_form)) {
            $formClass = Kwc_Admin::getComponentClass($this, 'FrontendForm');
            $this->_form = new $formClass('form', $this->getData()->componentClass);
        }
    }

    //form doesn't use processInput anymore
    protected final function _processInput($postData)
    {
    }

    protected final function _getIdFromPostData($postData)
    {
        if (!empty($postData[$this->getData()->componentId.'-id']) && !empty($postData[$this->getData()->componentId.'-idHash'])) {
            if ($postData[$this->getData()->componentId.'-idHash'] == Kwf_Util_Hash::hash($postData[$this->getData()->componentId.'-id'])) {
                //TODO: if hash doesn't match -> exception
                //TODO: remove component id from field name
                return $postData[$this->getData()->componentId.'-id'];
            }
        }
        return null;
    }

    public function processAjaxInput($postData)
    {
        $this->getForm()->trlStaticExecute($this->getData()->getLanguage());
        $this->_formTrlStaticExecuted = true;


        if ($this->_getIdFromPostData($postData)) {
            $this->getForm()->setId($this->_getIdFromPostData($postData));
        }

        $m = $this->getForm()->getModel();
        while ($m instanceof Kwf_Model_Proxy) {
            $m = $m->getProxyModel();
        }

        if (Kwf_Registry::get('db') && $m instanceof Kwf_Model_Db) {
            Kwf_Registry::get('db')->beginTransaction();
        }
        $postData = $this->_form->processInput(null, $postData);

        ignore_user_abort(true);
        $this->_errors = array_merge($this->_errors, $this->_validate($postData));
        if (!$this->_errors) {
            try {
                $this->_form->prepareSave(null, $postData);
                $isInsert = false;
                if (!$this->_form->getRow()->{$this->_form->getModel()->getPrimaryKey()}) {
                    $isInsert = true;
                    $this->_beforeInsert($this->_form->getRow());
                } else {
                    $this->_beforeUpdate($this->_form->getRow());
                }
                $this->_beforeSave($this->_form->getRow());
                $this->_form->save(null, $postData);
                if ($isInsert) {
                    $this->_afterInsert($this->_form->getRow());
                } else {
                    $this->_afterUpdate($this->_form->getRow());
                }
                $this->_form->afterSave(null, $postData);
                $this->_afterSave($this->_form->getRow());
            } catch (Exception $e) {
                $this->_handleProcessException($e);
            }
        }


        if (Kwf_Registry::get('db') && $m instanceof Kwf_Model_Db) {
            Kwf_Registry::get('db')->commit();
        }
    }

    //can be overriden to implement custom validation logic
    protected function _validate($postData)
    {
        return $this->_form->validate(null, $postData);
    }

    //can be overriden to *not* log specific exceptions or adapt error
    protected function _handleProcessException(Exception $e)
    {
        if ($e instanceof Kwf_Exception_Client) {
            $this->_errors[] = array(
                'message' => $e->getMessage()
            );
        } else {
            if (!$e instanceof Kwf_Exception) $e = new Kwf_Exception_Other($e);
            $e->logOrThrow();
            $this->_errors[] = array(
                'message' => trlKwf('An error occured while processing the form. Please try to submit again later.')
            );
        }
    }

    public function getPostData()
    {
        throw new Kwf_Exception('Removed. Probalby use NonAjax Form');
    }

    public function isPosted()
    {
        throw new Kwf_Exception('Removed. Probalby use NonAjax Form');
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getFormRow()
    {
        throw new Kwf_Exception('Removed. Probalby use NonAjax Form');
    }

    public function getForm()
    {
        if (!$this->_initialized) {
            $this->_initialized = true;
            $this->_initForm();

            $this->_form->initFields();
        }
        return $this->_form;
    }

    public function getSuccessComponent()
    {
        return $this->getData()->getChildComponent('-success');
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = Kwc_Abstract::getTemplateVars($renderer);

        if (!$this->_formTrlStaticExecuted) {
            $this->getForm()->trlStaticExecute($this->getData()->getLanguage());
            $this->_formTrlStaticExecuted = true;
        }

        $ret['errors'] = Kwf_Form::formatValidationErrors($this->getErrors());

        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            if ($c->id != 'success') {
                $ret[$c->id] = $c;
            }
        }

        $values = $this->getForm()->load(null, array());
        $idPrefix = $this->getData()->componentId.'_';
        if (Kwf_Config::getValue('application.uniquePrefix')) $idPrefix = Kwf_Config::getValue('application.uniquePrefix').'-'.$idPrefix;
        $ret['form'] = $this->getForm()->getTemplateVars($values, '', $idPrefix);

        $dec = $this->_getSetting('decorator');
        if ($dec && is_string($dec)) {
            $dec = new $dec();
            $ret['form'] = $dec->processItem($ret['form'], $this->getErrors());
        }

        $ret['formName'] = $this->getData()->componentId;

        $ret['formId'] = $this->getForm()->getId();
        if ($ret['formId']) {
            $ret['formIdHash'] = Kwf_Util_Hash::hash($ret['formId']);
        }

        $ret['message'] = null;

        $ret['rootElementClass'] .= ' kwfUp-webForm kwfUp-kwcForm';

        if ($ret['errors']) {
            $ret['rootElementClass'] .= ' kwfUp-kwcFormError kwfUp-webFormError';
        }

        $cacheId = 'kwcFormCu-'.get_class($this);
        $controllerUrl = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if (!$controllerUrl) {
            $controllerUrl = Kwc_Admin::getInstance(get_class($this))->getControllerUrl('FrontendForm');
            Kwf_Cache_SimpleStatic::add($cacheId, $controllerUrl);
        }
        $hideForValue = array();
        foreach ($this->_form->getHideForValue() as $v) {
            $hideForValue[] = array(
                'field' => $v['field']->getFieldName(),
                'value' => $v['value'],
                'hide' => $v['hide']->getFieldName(),
            );
        }

        $baseParams = $this->_getBaseParams();
        $baseParams['componentId'] = $this->getData()->componentId;

        $fieldConfig = array();
        $iterator = new RecursiveIteratorIterator(new Kwf_Collection_Iterator_RecursiveFormFields($this->_form->fields), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $field) {
            if ($field->getFieldName()) {
                $fieldConfig[$field->getFieldName()] = (object)$field->getFrontendMetaData();
            }
        }
        $errorStyle = $this->_getSetting('errorStyle');
        if (!$errorStyle) $errorStyle = Kwf_Config::getValue('kwc.form.errorStyle');
        $ret['config'] = array(
            'controllerUrl' => $controllerUrl,
            'hideFormOnSuccess' => $this->_getSetting('hideFormOnSuccess'),
            'componentId' => $this->getData()->componentId,
            'hideForValue' => $hideForValue,
            'fieldConfig' => (object)$fieldConfig,
            'errorStyle' => $errorStyle,
            'baseParams' => $baseParams,
            'submitDataLayer' => $this->getSubmitDataLayer()
        );

        $ret['uniquePrefix'] = Kwf_Config::getValue('application.uniquePrefix');
        if ($ret['uniquePrefix']) $ret['uniquePrefix'] .= '-';

        $ret['submitCaption'] = $this->_getPlaceholder('submitButton');

        return $ret;
    }

    /**
     * Return base params that will be sent with Ajax request. Won't be used for fallback POST request.
     *
     * @return array
     */
    protected function _getBaseParams()
    {
        return array();
    }

    // used by Kwc_Form_FrontendFormController
    public function getPlaceholder($placeholder = null)
    {
        return $this->_getPlaceholder($placeholder);
    }

    public function hasContent()
    {
        return true;
    }

    public function isProcessed()
    {
        throw new Kwf_Exception('Removed. Probalby use NonAjax Form');
    }

    public function isSaved()
    {
        throw new Kwf_Exception('Removed. Probalby use NonAjax Form');
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterUpdate(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeUpdate(Kwf_Model_Row_Interface $row)
    {
    }
}
