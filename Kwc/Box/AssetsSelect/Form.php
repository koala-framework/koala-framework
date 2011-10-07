<?php
class Vpc_Box_AssetsSelect_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $sections = array();
        $vars = new Zend_Config_Ini('application/assetVariables.ini');
        foreach ($vars as $i=>$j) {
            $sections[$i] = $i == 'web' ? trlVps('Standard') : $i;
        }
        $this->fields->add(new Vps_Form_Field_Select('section', trlVps('Section')))
            ->setAllowBlank(false)
            ->setValues($sections)
            ->setDefaultValue('web')
            ->setWidth(120);

    }
}
