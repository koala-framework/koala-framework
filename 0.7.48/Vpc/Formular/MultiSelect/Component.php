<?php
class Vpc_Formular_MultiSelect_Component extends Vpc_Formular_Select_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.MultiSelect',
            'tablename' => 'Vpc_Formular_MultiSelect_Model',
            'default' => array(
                'type' => 'checkbox',
                'size' => '5'
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['template'] = 'Formular/MultiSelect.html';
        return $return;
    }

    public function processInput()
    {
        $name = $this->_getName();
        foreach ($this->getOptions() AS $key => $option) {
            $this->_options[$key]['checked'] = isset($_POST[$name]) && in_array($option['value'], $_POST[$name]);
        }
    }

}
