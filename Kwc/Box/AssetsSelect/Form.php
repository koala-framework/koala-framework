<?php
class Kwc_Box_AssetsSelect_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $sections = array();
        $vars = new Zend_Config_Ini('assetVariables.ini');
        foreach ($vars as $i=>$j) {
            $sections[$i] = $i == 'web' ? trlKwf('Standard') : $i;
        }
        $this->fields->add(new Kwf_Form_Field_Select('section', trlKwf('Section')))
            ->setAllowBlank(false)
            ->setValues($sections)
            ->setDefaultValue('web')
            ->setWidth(120);

    }
}
