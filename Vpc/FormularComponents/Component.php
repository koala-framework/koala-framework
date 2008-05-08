<?php
class Vpc_Formular_Component extends Vpc_Paragraphs_Component
{
    private $_errors = array();
    private $_successComponent;

    public static function getSettings()
    {
        static $settings;
        if (!isset($settings)) {
            $settings = array_merge(parent::getSettings(), array(
                'componentName' => 'Formular',
                'tablename' => 'Vpc_Formular_Model'
            ));
            $settings['childComponentClasses'] =
                Vpc_Admin::getInstance('Vpc_Formular_Component')->getComponents();
            $settings['childComponentClasses']['success'] = 'Vpc_Formular_Success_Component';
        }
        return $settings;
    }
    protected function _getParagraphs()
    {
        $childComponents = parent::_getParagraphs();
        foreach ($childComponents as $id => $component) {
            foreach ($this->_getRows() as $row) {
                if ($row->id == $id) {
                    $isMandatory =
                        $row->mandatory == 1 ||
                        $component instanceof Vpc_Formular_Captcha_Component;
                    $component->store('fieldLabel', $row->field_label);
                    $component->store('isMandatory', $isMandatory);
                    $component->store('noCols', $row->no_cols == 1);
                    $component->store('isValid', true);
                    if ($component instanceof Vpc_Formular_Field_Interface ) {
                        $component->store('name', $component->getId());
                    }
                }
            }
        }
        return $childComponents;
    }

    public function getChildComponents()
    {
        $childComponents = parent::getChildComponents();
        $childComponents[] = $this->_getSuccessComponent();
        return $childComponents;
    }

    public function getTemplateVars()
    {
        $sent = 1;
        $values = array();
        if ($_POST != array()) {
            if ($this->_validateFields()) {
                $values = $this->_getValues();

                try {
                    $this->_processForm($values);
                    $sent = 3;
                } catch (Vps_ClientException $e) {
                    $addError = $e->getMessage();
                    $sent = 2;
                }
            } else {
                $sent = 2;
            }
        }
        $vars = parent::getTemplateVars();
        $vars['sent'] = $sent;
        $vars['action'] = $_SERVER['REQUEST_URI'];
        $vars['errors'] = $this->_errors;
        if (isset($addError) && !empty($addError)) $vars['errors'][] = $addError;
        $vars['upload'] = $this->_hasUpload();
        $vars['values'] = $values;
        $vars['success'] = $this->_getSuccessComponent()->getTemplateVars();
        return $vars;
    }

    protected function _getValues()
    {
        $values = array();
        foreach ($this->getChildComponents() as $component) {
            if ($component instanceof Vpc_Formular_Field_Interface) {
                $values[$component->getStore('fieldLabel')] = $component->getValue();
            }
        }
        return $values;
    }

    protected function _processForm($values)
    {
    }

    private function _hasUpload()
    {
        foreach ($this->getChildComponents() as $value => $component) {
            if ($component instanceof Vpc_Formular_FileUpload_Component) {
                return true;
            }
        }
        return false;
    }

    private function _validateFields()
    {
        $return = true;
        foreach ($this->getChildComponents() as $component) {
            if ($component instanceof Vpc_Formular_Field_Interface) {
                $component->processInput();
                $message = $component->validateField($component->getStore('isMandatory'));
                if ($message != '') {
                    $return = false;
                    $this->_errors[] = $message;
                    $component->store('isValid', false);
                }
            }
        }
        return $return;
    }

    protected function _createFieldComponent($class, $row)
    {
        if (!Vps_Loader::classExists($class)) {
            $class = "Vpc_Formular_{$class}_Component";
        }
        $c = Vpc_Abstract::_createInstance($this->getDao(), $class, (object)$row,
                                    $this->getDbId(), $this->getPageCollection());
        $c->store('noCols', false);
        $c->store('isValid', true);
        $c->store('isMandatory', false);
        $c->store('fieldLabel', '');

        $this->_paragraphs[] = $c;
        return $c;
    }

    protected function _getSuccessComponent()
    {
        if (!isset($this->_successComponent)) {
            $classes = $this->_getSetting('childComponentClasses');
            $this->_successComponent = $this->createComponent($classes['success'], 'success');
        }
        return $this->_successComponent;
    }
}
