<?php
class Vpc_Formular_MultiSelect_Component extends Vpc_Formular_Select_Component
{
    protected $_settings = array(
        'name' => '',
        'type' => 'checkbox',
        'size' => '5'
    );
    protected $_tablename = 'Vpc_Formular_MultiSelect_Model';
    const NAME = 'Formular.MultiSelect';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['template'] = 'Formular/MultiSelect.html';
        return $return;
    }

    public function processInput()
    {
        $name = $this->getSetting('name');
        foreach ($this->getOptions() AS $key => $option) {
            $this->_options[$key]['checked'] = isset($_POST[$name]) && in_array($option['value'], $_POST[$name]);
        }
    }

}
