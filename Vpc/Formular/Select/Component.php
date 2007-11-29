<?php
class Vpc_Formular_Select_Component extends Vpc_Formular_Field_Abstract
{
    protected $_settings = array(
        'name' => '',
        'type' => 'radio'
    );
    protected $_tablename = 'Vpc_Formular_Select_Model';
    const NAME = 'Formular.Select';
    protected $_options;

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['options'] = $this->getOptions();
        $return['type'] = $this->getSetting('type');
        $return['name'] = $this->getSetting('name');
        $return['size'] = $this->getSetting('size');
        $return['template'] = 'Formular/Select.html';
        return $return;
    }

    public function getOptions()
    {
        if (!$this->_options) {
            $table = $this->getTable('Vpc_Formular_Select_OptionsModel');
            $where = array(
                'page_id = ?' => $this->getDbId(),
                'component_key = ?' => $this->getComponentKey()
            );
            $rows = $table->fetchAll($where);
            $options = array();
            foreach ($rows as $row) {
                $this->_options[] = array(
                    'value' => $row->id,
                    'text' => $row->text,
                    'checked' => $row->checked,
                    'id' => $row->id
                );
            }
        }

        return $this->_options;
    }

    public function processInput()
    {
        if (isset($_POST[$this->getSetting('name')])) {
            foreach ($this->getOptions() AS $key => $option) {
                $this->_options[$key]['checked'] = $option['value'] == $_POST[$this->getSetting('name')];
            }
        }
    }

    public function validateField($mandatory)
    {
        if ($mandatory && !isset($_POST[$this->getSetting('name')])) {
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return '';
    }
}
