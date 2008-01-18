<?php
class Vpc_Formular_Textarea_Component extends Vpc_Formular_Field_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Textarea',
            'tablename' => 'Vpc_Formular_Textarea_Model',
            'default' => array(
                'width' => '150',
                'height' => '50',
                'name' => '',
                'value' => ''
            )
        ));
    }

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['value'] = $this->_getRow()->value;
        $return['width'] = $this->_getRow()->width;
        $return['height'] = $this->_getRow()->height;
        if (isset($this->_getRow()->name)) {
            $return['name'] = $this->_getRow()->name;
        } else {
            $return['name'] = $this->_store['name'];
        }

        return $return;
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
}
