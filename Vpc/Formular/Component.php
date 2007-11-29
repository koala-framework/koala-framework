<?php
class Vpc_Formular_Component extends Vpc_Paragraphs_Abstract
{
    private $_errors = array();

    public static function getSettings()
    {
        static $settings;
        if (!isset($settings)) {
            $settings = array_merge(parent::getSettings(), array(
                'componentName' => 'Formular',
                'hideInParagraphs' => false,
                'tablename' => 'Vpc_Formular_Model'
            ));
            $settings['childComponentClasses'] = Vpc_Admin::getInstance('Vpc_Formular_Component')
                                    ->getComponents();
        }
        return $settings;
    }

    protected function _init()
    {
        parent::_init();
        foreach ($this->_getRows() as $row) {
            $component = $this->_paragraphs[$row->id];
            $component->store('fieldLabel', $row->field_label);
            $component->store('isMandatory', $component instanceof Vpc_Formular_Captcha_Component || $row->mandatory == 1);
            $component->store('noCols', $row->no_cols == 1);
            $component->store('isValid', true);
            if ($component instanceof Vpc_Formular_Field_Interface ) {
                $component->store('name', $component->getId());
            }
        }
    }


    public function getTemplateVars()
    {
        $sent = 1;
        if ($_POST != array()) {
            if ($this->_validateFields()) {
                $this->_processForm();
                $sent = 3;
            } else {
                $sent = 2;
            }
        }
        $vars = parent::getTemplateVars();
        $vars['sent'] = $sent;
        $vars['action'] = $_SERVER['REQUEST_URI'];
        $vars['errors'] = $this->_errors;
        $vars['upload'] = $this->_hasUpload();
        return $vars;
    }

    protected function _processForm()
    {
    }

    public function getChildComponents()
    {
        return $this->_paragraphs;
    }
    
/*
    private function _getPreparedData()
    {
        if (!$this->_data) {
            $data = $this->_getData()->toArray();
            $hasSubmit = false;
            foreach ($data as $d) {
                if ($d['component_class'] == 'Vpc_Formular_Submit_Component') {
                    $hasSubmit = true;
                }
            }
            if (!$hasSubmit) {
                $d['id'] = 0;
                $d['component_class'] = 'Vpc_Formular_Submit_Component';
                $d['name'] = 'submit';
                $d['description'] = '';
                $d['no_cols'] = '1';
                $d['mandatory'] = '0';
                $data[] = $d;
            }
            $this->_data = $data;
        }
        return $this->_data;
    }
*/


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

}
