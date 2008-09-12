<?php
class Vpc_Form_Component extends Vpc_Abstract_Composite_Component
{
    protected $_form;
    private $_processed = false;
    private $_isSaved = false;
    private $_postData;
    protected $_errors = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Form_Success_Component';
        $ret['componentName'] = trlVps('Formular');
        $ret['placeholder']['submitButton'] = trlVps('Submit');
        $ret['placeholder']['error'] = trlVps('An error has occurred');
        $ret['decorator'] = 'Vpc_Form_Decorator_Label';
        $ret['viewCache'] = false;
        $ret['method'] = 'post';
        $ret['cssClass'] = 'webStandard webForm';
        
        //todo: wenn mehrere verbessern
        $ret['assets']['files'][] = 'vps/Vps/Form/Field/File/Component.css';

        $ret['flags']['processInput'] = true;
        return $ret;
    }

    protected function _initForm()
    {
        if (!isset($this->_form)) {
            $this->_form = Vpc_Abstract_Form::createComponentForm(get_class($this), 'form');
        }
    }

    public function processInput(array $postData)
    {
        if ($this->_processed) {
            throw new Vps_Exception("ProcessInput has been called allready for {$this->getData()->componentId}");
        }
        $this->_processed = true;

        $this->_initForm();

        Vps_Registry::get('db')->beginTransaction();

        $this->_form->initFields();

        $postData = $this->_form->processInput($postData);
        if (isset($postData[$this->getData()->componentId])) {
            $this->_errors = array_merge($this->_errors, $this->_form->validate($this->_form->getRow(), $postData));
            if (!$this->_errors) {
                $this->_form->prepareSave(null, $postData);
                $this->_beforeSave($this->_form->getRow());
                if (!$this->_form->getRow()->{$this->_form->getModel()->getPrimaryKey()}) {
                    $this->_beforeInsert($this->_form->getRow());
                }
                $this->_form->save(null, $postData);
                $this->_afterSave($this->_form->getRow());
                if (!$this->_form->getRow()->{$this->_form->getModel()->getPrimaryKey()}) {
                    $this->_afterInsert($this->_form->getRow());
                }
                $this->_isSaved = true;
            }
        }
        $this->_postData = $postData;

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
        return $this->_form->getRow();
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
        if (isset($_POST[$this->getData()->componentId])) {
            if (!$ret['errors'] && $class) {
                $ret['showSuccess'] = true;
            }
        }

        $values = $this->_form->load(null, $this->_postData);
        $ret['form'] = $this->_form->getTemplateVars($values);

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
                new Vps_Collection_Iterator_RecursiveFormFields($this->_form->fields))
                as $f) {
            if ($f instanceof Vps_Form_Field_File) {
                $ret['isUpload'] = true;
                break;
            }
        }

        return $ret;
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
