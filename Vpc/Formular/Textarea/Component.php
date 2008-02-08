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
        return $return;
    }

    public function processInput()
    {
        if (isset($_POST[$this->_getName()])) {
            $this->_getRow()->value = $_POST[$this->_getName()];
        }
    }
}
