<?php
class Vpc_Form_Component extends Vpc_Abstract_Composite_Component
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
        $ret['generators']['child']['component']['success'] = 'Vpc_Form_Success_Component';
        $ret['componentName'] = trlVps('Form');
        $ret['placeholder']['submitButton'] = trlVpsStatic('Submit');
        $ret['placeholder']['error'] = trlVpsStatic('An error has occurred');
        $ret['decorator'] = 'Vpc_Form_Decorator_Label';
        $ret['viewCache'] = false;
        $ret['method'] = 'post';

        //todo: wenn mehrere verbessern
        $ret['assets']['dep'][] = 'ExtElement';
        $ret['assets']['files'][] = 'vps/Vps/Form/Field/File/Component.css';
        $ret['assets']['files'][] = 'vps/Vps/Form/Field/MultiCheckbox/Component.js';
        $ret['assets']['files'][] = 'vps/Vpc/Form/Component.js';
        $ret['assets']['files'][] = 'vps/Vps_js/Form/FieldSet/Component.js';

        $ret['flags']['processInput'] = true;

        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';

        $ret['buttonClass'] = 'vpsButtonFlat'; //um standard styles aus dem Vps zu umgehen
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);

        // wenn es eine Form.php gibt aber keine FrontendForm.php
        // sollte man aus irgendeinem grund doch eine Form benutzen ohne FrontendForm
        // dann einfach validateSettings überschreiben und parent nicht aufrufen
        $frontendFormClass = Vpc_Admin::getComponentClass($componentClass, 'FrontendForm');
        $formClass = Vpc_Admin::getComponentClass($componentClass, 'Form');
        if ($formClass != 'Vpc_Abstract_Composite_Form' && !$frontendFormClass) {
            throw new Vps_Exception("Form.php files for frontend have been renamed to FrontendForm.php");
        }

        if ($frontendFormClass && is_instance_of($frontendFormClass, 'Vpc_Abstract_Form')) {
            throw new Vps_Exception("A frontend form may never be an instance of Vpc_Abstract_Form");
        }
    }

    protected function _initForm()
    {
        if (!isset($this->_form)) {
            $formClass = Vpc_Admin::getComponentClass($this, 'FrontendForm');
            $this->_form = new $formClass('form', get_class($this));
        }
    }

    public function preProcessInput(array $postData)
    {
    }

    public function processInput(array $postData)
    {
        $this->_processInput($postData);
    }

    protected function _processInput($postData)
    {
        if ($this->_processed) {
            return;
        }
        $this->_processed = true;

        if (!$this->getForm()) return;

        $m = $this->getForm()->getModel();
        while ($m instanceof Vps_Model_Proxy) {
            $m = $m->getProxyModel();
        }
        if (Vps_Registry::get('db') && $m instanceof Vps_Model_Db) {
            Vps_Registry::get('db')->beginTransaction();
        }

        $this->getForm()->initFields();

        if (!isset($postData[$this->getData()->componentId.'-post']) && !isset($postData[$this->getData()->componentId])) {
            $postData = array();
            $this->_posted = false;
        } else {
            $this->_posted = true;
        }
        $postData = $this->_form->processInput(null, $postData);
        $this->_postData = $postData;
        if (isset($postData[$this->getData()->componentId])) {
            ignore_user_abort(true);
            $this->_errors = array_merge($this->_errors, $this->_form->validate(null, $postData));
            if (!$this->_errors) {
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
            }
        }

        if (Vps_Registry::get('db') && $m instanceof Vps_Model_Db) {
            Vps_Registry::get('db')->commit();
        }
    }

    public function getPostData()
    {
        if (!$this->_processed) {
            throw new Vps_Exception("Form '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }
        return $this->_postData;
    }

    public function getErrors()
    {
        if (!$this->_processed) {
            throw new Vps_Exception("Form '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }
        return $this->_errors;
    }

    public function getFormRow()
    {
        if (!$this->_processed) {
            throw new Vps_Exception("Form '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }
        return $this->getForm()->getRow();
    }

    public function getForm()
    {
        if (!$this->_initialized) {
            $this->_initialized = true;
            $this->_initForm();
            $this->_form->trlStaticExecute($this->getData()->getLanguage());
        }
        return $this->_form;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        if (!$this->_processed) {
            throw new Vps_Exception("Form '{$this->getData()->componentId}' has not yet been processed, processInput must be called");
        }

        $class = null;
        if (self::hasChildComponentClass(get_class($this), 'child', 'success')) {
            $class = self::getChildComponentClass(get_class($this), 'child', 'success');
        }

        $ret['isPosted'] = $this->_posted;
        $ret['showSuccess'] = false;
        $ret['errors'] = Vps_Form::formatValidationErrors($this->getErrors());
        if ($this->isSaved()) {
            if (!$ret['errors'] && $class) {
                $ret['showSuccess'] = true;
            }
        }

        $values = $this->getForm()->load(null, $this->_postData);
        $ret['form'] = $this->getForm()->getTemplateVars($values);

        $dec = $this->_getSetting('decorator');
        if ($dec && is_string($dec)) {
            $dec = new $dec();
            $ret['form'] = $dec->processItem($ret['form'], $this->getErrors());
        }

        $ret['formName'] = $this->getData()->componentId;
        $ret['buttonClass'] = $this->_getSetting('buttonClass');

        $ret['action'] = $this->getData()->url;
        $ret['method'] = $this->_getSetting('method');

        $ret['isUpload'] = false;
        foreach (new RecursiveIteratorIterator(
                new Vps_Collection_Iterator_RecursiveFormFields($this->getForm()->fields))
                as $f) {
            if ($f instanceof Vps_Form_Field_File) {
                $ret['isUpload'] = true;
                break;
            }
        }
        $ret['message'] = null;

        return $ret;
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

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
    }

    protected function _afterInsert(Vps_Model_Row_Interface $row)
    {
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
    }
}
