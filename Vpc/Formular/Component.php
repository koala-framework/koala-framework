<?php
class Vpc_Formular_Component extends Vpc_Abstract_Composite_Component
{
    protected $_form;
    protected $_formName;
    private $_processed = false;
    private $_errors;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['success'] = 'Vpc_Formular_Success_Component';
        $ret['componentName'] = 'Formular';
        $ret['placeholder']['submitButton'] = trlVps('Submit');
        $ret['decorator'] = 'Vpc_Formular_Decorator_Label';
        $ret['viewCache'] = false;
        return $ret;
    }

    protected function _initForm()
    {
    }

    protected function _processForm()
    {
        if ($this->_processed) return;
        $this->_processed = true;

        $this->_initForm();

        if (!isset($this->_form) && isset($this->_formName)) {
            $this->_form = new $this->_formName();
        }

        $this->_errors = array();
        if (isset($_POST[$this->getData()->componentId])) {
            $this->_errors = $this->_form->validate($_REQUEST);
            if (!$this->_errors) {
                $this->_form->prepareSave(null, $_REQUEST);
                $this->_beforeSave($this->_form->getRow());
                $this->_form->save(null, $_REQUEST);
                $this->_afterSave($this->_form->getRow());
            }
        }
    }

    public function getErrors()
    {
        $this->_processForm();
        return $this->_errors;
    }

    public function getFormRow()
    {
        $this->_processForm();
        return $this->_form->getRow();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $this->_processForm();

        $classes = $this->_getSetting('childComponentClasses');

        $ret['showSuccess'] = false;
        $ret['errors'] = $this->getErrors();
        if (isset($_POST[$this->getData()->componentId])) {
            if (!$ret['errors'] && $classes['success']) {
                $ret['showSuccess'] = true;
            }
        }

        $values = array_merge($this->_form->load(null), $_REQUEST);
        $ret['form'] = $this->_form->getTemplateVars($values);

        $dec = $this->_getSetting('decorator');
        if ($dec && is_string($dec)) {
            $dec = new $dec();
            $ret['form'] = $dec->processItem($ret['form']);
        }

        $ret['formName'] = $this->getData()->componentId;

        $ret['action'] = $this->getData()->url;

        return $ret;
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
    }
}
