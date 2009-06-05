<?php
class Vpc_Form_Component extends Vpc_Abstract_Composite_Component
{
    protected $_form;
    private $_processed = false;
    private $_isSaved = false;
    private $_initialized = false;
    private $_postData;
    protected $_errors = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Form_Success_Component';
        $ret['componentName'] = trlVps('Form');
        $ret['placeholder']['submitButton'] = trlVps('Submit');
        $ret['placeholder']['error'] = trlVps('An error has occurred');
        $ret['decorator'] = 'Vpc_Form_Decorator_Label';
        $ret['viewCache'] = false;
        $ret['method'] = 'post';

        //todo: wenn mehrere verbessern
        $ret['assets']['files'][] = 'vps/Vps/Form/Field/File/Component.css';
        $ret['assets']['files'][] = 'vps/Vpc/Form/Component.js';

        $ret['flags']['processInput'] = true;
        return $ret;
    }

    protected function _initForm()
    {
        if (!isset($this->_form)) {
            $this->_form = Vpc_Abstract_Form::createComponentForm(get_class($this), 'form');
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
            throw new Vps_Exception("ProcessInput has been called already for {$this->getData()->componentId}");
        }
        $this->_processed = true;

        if (!$this->getForm()) return;

        Vps_Registry::get('db')->beginTransaction();

        $this->getForm()->initFields();

        if (!isset($postData[$this->getData()->componentId.'-post']) && !isset($postData[$this->getData()->componentId])) {
            $postData = array();
        }
        $postData = $this->_form->processInput($this->_form->getRow(), $postData);
        $this->_postData = $postData;
        if (isset($postData[$this->getData()->componentId])) {
            ignore_user_abort(true);
            $this->_errors = array_merge($this->_errors, $this->_form->validate($this->_form->getRow(), $postData));
            if (!$this->_errors) {
                $this->_form->prepareSave(null, $postData);
                $this->_beforeSave($this->_form->getRow());
                $isInsert = false;
                if (!$this->_form->getRow()->{$this->_form->getModel()->getPrimaryKey()}) {
                    $isInsert = true;
                    $this->_beforeInsert($this->_form->getRow());
                }
                $this->_form->save(null, $postData);
                $this->_afterSave($this->_form->getRow());
                if ($isInsert) {
                    $this->_afterInsert($this->_form->getRow());
                }
                $this->_isSaved = true;
            }
        }

        Vps_Registry::get('db')->commit();
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
        $ret['showSuccess'] = false;
        $ret['errors'] = $this->getErrors();
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
            $ret['form'] = $dec->processItem($ret['form']);
        }

        $ret['formName'] = $this->getData()->componentId;

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
