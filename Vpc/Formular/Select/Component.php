<?php
class Vpc_Formular_Select_Component extends Vpc_Formular_Field_Abstract
{
    protected $_options;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Select',
            'tablename' => 'Vpc_Formular_Select_Model',
            'default' => array(
                'width' => '',
                'name' => '',
                'value' => '',
                'type' => 'radio',
                'validator' => ''
            )
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Formular/Select/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['options'] = $this->getOptions();
        $return['type'] = $this->_getRow()->type;
        $return['name'] = $this->_getRow()->name;
        $return['width'] = $this->_getRow()->width;
        return $return;
    }

    public function setOptions(array $options)
    {
        $this->_options = $options;
        foreach ($this->_options as $key => $option) {
            if (!isset($option['checked'])) $this->_options[$key]['checked'] = 0;
        }
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
            $this->_options = array();
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

    protected function _getName()
    {
        if (isset($this->_getRow()->name)) {
            //subotimal
            return $this->_getRow()->name;
        } else {
            return $this->_store['name'];
        }
    }

    public function processInput()
    {
        $name = $this->_getName();
        if (isset($_POST[$name])) {
            $this->_getRow()->value = $_POST[$name];
        }
    }

    public function validateField($mandatory)
    {
        if ($mandatory && !isset($_POST[$this->_getName()])) {
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return '';
    }
}
