<?php
class Vpc_Formular_SelectMulti_Index extends Vpc_Formular_Field_Abstract
{
    protected $_settings = array(
        'name' => '',
        'horizontal' => 0
    );
    protected $_tablename = 'Vpc_Formular_Multicheckbox_IndexModel';
    public $controllerClass = 'Vpc_Formular_Multicheckbox_IndexController';
    const NAME = 'Formular.SelectMulti';
    private $_checkboxes;

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['horizontal'] = $this->getSetting('horizontal');
        foreach ($this->getChildComponents() as $component) {
            $return['checkboxes'][] = $component->getTemplateVars();
        }
        $return['template'] = 'Formular/SelectMulti.html';
        return $return;
    }

    public function getChildComponents()
    {
        if (!$this->_checkboxes) {
            $table = $this->_getTable('Vpc_Formular_Multicheckbox_CheckboxesModel');
            $where = array(
                'page_id = ?' => $this->getDbId(),
                'component_key = ?' => $this->getComponentKey()
            );
            $rows = $table->fetchAll($where);
            $components = array();
            foreach ($rows as $row){
                $component = $this->createComponent('Vpc_Formular_Checkbox_Index', $row->id);
                $component->setSetting('name', $this->getSetting('name') . '[]');
                $component->setSetting('value', $component->getId());
                $component->setSetting('checked', $row->checked == 1);
                $component->setSetting('text', $row->text);
                $this->_checkboxes[] = $component;
            }
        }

        return $this->_checkboxes;
    }

    public function processInput()
    {
        if (isset($_POST)) {
            $values = isset($_POST[$this->getSetting('name')]) ? $_POST[$this->getSetting('name')] : array();
            foreach ($this->getChildComponents() as $component) {
                if (array_search($component->getSetting('value'), $values) !== false) {
                    $component->setSetting('checked', true);
                } else {
                    $component->setSetting('checked', false);
                }
            }
        }
    }

    public function validateField($mandatory)
    {
        if ($mandatory && !isset($_POST[$this->getSetting('name')])) {
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, zumindest ein Feld markieren';
        }
        return '';
    }
    
}