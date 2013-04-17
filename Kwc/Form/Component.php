<?php
class Kwc_Form_Component extends Kwc_Abstract_Composite_Component
{
    protected $_form;
    private $_processed = false;
    private $_isSaved = false;
    private $_initialized = false;
    private $_posted;
    private $_postData;
    protected $_errors = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Kwc_Form_Success_Component';
        $ret['componentName'] = trlKwfStatic('Form');
        $ret['placeholder']['submitButton'] = trlKwfStatic('Submit');
        $ret['placeholder']['error'] = trlKwfStatic('An error has occurred');
        $ret['decorator'] = 'Kwc_Form_Decorator_Label';
        $ret['viewCache'] = true;
        $ret['method'] = 'post';

        $plugins = array('Kwc_Form_UseViewCachePlugin');
        if (isset($ret['plugins'])) {
            $ret['plugins'] = array_merge($ret['plugins'], $plugins);
        } else {
            $ret['plugins'] = $plugins;
        }

        //todo: wenn mehrere verbessern
        $ret['assets']['dep'][] = 'ExtElement';
        $ret['assets']['dep'][] = 'ExtFx'; //for .fadeIn()
        $ret['assets']['dep'][] = 'ExtDomHelper';
        $ret['assets']['dep'][] = 'ExtConnection';
        $ret['assets']['dep'][] = 'ExtDateMenu';
        $ret['assets']['dep'][] = 'KwfEvents';
        $ret['assets']['files'][] = 'kwf/Kwc/Form/Component.js';
        $ret['assets']['files'][] = 'kwf/Kwf_js/FrontendForm/Field.js';
        $ret['assets']['files'][] = 'kwf/Kwf_js/FrontendForm/ErrorStyle/Abstract.js';
        $ret['assets']['files'][] = 'kwf/Kwf_js/FrontendForm/ErrorStyle/Above.js';
        $ret['assets']['files'][] = 'kwf/Kwf_js/FrontendForm/*';

        $ret['useAjaxRequest'] = true;
        $ret['hideFormOnSuccess'] = true; // works only when useAjaxRequest==true

        $ret['flags']['processInput'] = true;

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        $ret['buttonClass'] = 'kwfButtonFlat'; //um standard styles aus dem Kwf zu umgehen

        $ret['errorStyle'] = null; //default from config.ini: kwc.form.errorStyle

        return $ret;
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

    public function preProcessInput(array $postData)
    {
    }

    public function processInput(array $postData)
    {
        $this->_processInput($postData);
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

    protected function _processInput($postData)
    {
        if ($this->_processed) {
            return;
        }
        $this->_setProcessed();

        if (!$this->getForm()) return;

        if ($this->_getIdFromPostData($postData)) {
            $this->getForm()->setId($this->_getIdFromPostData($postData));
        }

        $m = $this->getForm()->getModel();
        while ($m instanceof Kwf_Model_Proxy) {
            $m = $m->getProxyModel();
        }

        $this->getForm()->initFields();

        if (!isset($postData[$this->getData()->componentId.'-post']) && !isset($postData[$this->getData()->componentId])) {
            $postData = array();
            $this->_posted = false;
        } else {
            $this->_posted = true;
        }
        if ($this->_posted && Kwf_Registry::get('db') && $m instanceof Kwf_Model_Db) {
            Kwf_Registry::get('db')->beginTransaction();
        }
        $postData = $this->_form->processInput(null, $postData);
        $this->_postData = $postData;
        if (isset($postData[$this->getData()->componentId])) {
            ignore_user_abort(true);
            $this->_errors = array_merge($this->_errors, $this->_form->validate(null, $postData));
            if (!$this->_errors) {
                try {
                    $this->_form->prepareSave(null, $postData);
                    $this->_beforeSave($this->_form->getRow());
                    $isInsert = false;
                    if (!$this->_form->getRow()->{$this->_form->getModel()->getPrimaryKey()}) {
                        $isInsert = true;
                        $this->_beforeInsert($this->_form->getRow());
                    }
                    $this->_form->save(null, $postData);
                    $this->_form->afterSave(null, $postData);
                    $this->_afterSave($this->_form->getRow());
                    if ($isInsert) {
                        $this->_afterInsert($this->_form->getRow());
                    }
                    $this->_isSaved = true;
                } catch (Exception $e) {
                    $this->_handleProcessException($e);
                }
            }
        }

        if ($this->_posted && Kwf_Registry::get('db') && $m instanceof Kwf_Model_Db) {
            Kwf_Registry::get('db')->commit();
        }

        if ($this->isSaved() && !$this->_errors &&
            $this->getSuccessComponent() && $this->getSuccessComponent()->isPage &&
            (!isset($postData['doNotRelocate']) || !$postData['doNotRelocate'])
        ) {
            header('Location: ' . $this->getSuccessComponent()->url);
            exit;
        }
    }

    //can be overriden to *not* log specific exceptions or adapt error
    protected function _handleProcessException(Exception $e)
    {
        if ($e instanceof Kwf_Exception_Client) {
            $this->_errors[] = array(
                'message' => $e->getMessage()
            );
        } else {
            if (!$e instanceof Kwf_Exception_Abstract) $e = new Kwf_Exception_Other($e);
            $e->logOrThrow();
            $this->_errors[] = array(
                'message' => trlKwf('An error occured while processing the form. Please try to submit again later.')
            );
        }
    }

    public function getPostData()
    {
        if (!$this->_processed) {
            throw new Kwf_Exception("Form '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }
        return $this->_postData;
    }

    public function getErrors()
    {
        if (!$this->_processed) {
            throw new Kwf_Exception("Form '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }
        return $this->_errors;
    }

    public function getFormRow()
    {
        if (!$this->_processed) {
            throw new Kwf_Exception("Form '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }
        return $this->getForm()->getRow();
    }

    public function getForm()
    {
        if (!$this->_initialized) {
            $this->_initialized = true;
            $this->_initForm();
            if ($this->_form) $this->_form->trlStaticExecute($this->getData()->getLanguage());
        }
        return $this->_form;
    }

    public function getSuccessComponent()
    {
        return $this->getData()->getChildComponent('-success');
    }

    public function getTemplateVars()
    {
        $ret = Kwc_Abstract::getTemplateVars();

        if (!$this->_processed) {
            throw new Kwf_Exception("Form '{$this->getData()->componentClass}' with id '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }

        $ret['isPosted'] = $this->_posted;
        $ret['showSuccess'] = false;
        $ret['errors'] = Kwf_Form::formatValidationErrors($this->getErrors());
        if ($this->isSaved()) {
            if (!$ret['errors'] && $this->getSuccessComponent()) {
                $ret['showSuccess'] = true;
            }
        }

        if ($ret['showSuccess']) {
            foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
                $ret[$c->id] = $c;
            }
        }

        $values = $this->getForm()->load(null, $this->_postData);
        $ret['form'] = $this->getForm()->getTemplateVars($values, '', $this->getData()->componentId.'_');

        $dec = $this->_getSetting('decorator');
        if ($dec && is_string($dec)) {
            $dec = new $dec();
            $ret['form'] = $dec->processItem($ret['form'], $this->getErrors());
        }

        $ret['formName'] = $this->getData()->componentId;
        $ret['buttonClass'] = $this->_getSetting('buttonClass');

        $ret['formId'] = $this->getForm()->getId();
        if ($ret['formId']) {
            $ret['formIdHash'] = Kwf_Util_Hash::hash($ret['formId']);
        }

        $page = $this->getData()->getPage();
        if (!$page) {
            throw new Kwf_Exception('Form must have an url so it must be on a page but is on "' . $this->getData()->componentId . '". (If component is a box it must not be unique)');
        }
        $cachedContent = Kwf_Component_Cache::getInstance()->load(
            $page->componentId, 'componentLink'
        );
        if ($cachedContent) {
            $targetPage = unserialize($cachedContent);
            $ret['action'] = $targetPage[0];
        } else {
            $ret['action'] = $this->getData()->url;
        }
        if (isset($_SERVER["QUERY_STRING"])) {
            $ret['action'] .= '?' . $_SERVER["QUERY_STRING"];
        }

        $ret['method'] = $this->_getSetting('method');

        $ret['isUpload'] = false;
        foreach (new RecursiveIteratorIterator(
                new Kwf_Collection_Iterator_RecursiveFormFields($this->getForm()->fields))
                as $f) {
            if ($f instanceof Kwf_Form_Field_File) {
                $ret['isUpload'] = true;
                break;
            }
        }
        $ret['message'] = null;

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
        $iterator = new RecursiveIteratorIterator(new Kwf_Collection_Iterator_RecursiveFormFields($this->_form->fields));
        foreach ($iterator as $field) {
            $fieldConfig[$field->getFieldName()] = (object)$field->getFrontendMetaData();
        }
        $errorStyle = $this->_getSetting('errorStyle');
        if (!$errorStyle) $errorStyle = Kwf_Config::getValue('kwc.form.errorStyle');
        $ret['config'] = array(
            'controllerUrl' => $controllerUrl,
            'useAjaxRequest' => $this->_getSetting('useAjaxRequest') && !$ret['isUpload'],
            'hideFormOnSuccess' => $this->_getSetting('hideFormOnSuccess'),
            'componentId' => $this->getData()->componentId,
            'hideForValue' => $hideForValue,
            'fieldConfig' => (object)$fieldConfig,
            'errorStyle' => $errorStyle,
            'baseParams' => $baseParams
        );

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

    protected function _setProcessed()
    {
        $this->_processed = true;
        return $this;
    }

    public function hasContent()
    {
        return true;
    }

    public function isProcessed()
    {
        return $this->_processed;
    }

    public function isSaved()
    {
        return $this->_isSaved;
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
}
