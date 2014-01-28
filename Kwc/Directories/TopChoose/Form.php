<?php
class Kwc_Directories_TopChoose_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->fields->add(new Kwf_Form_Field_Select('directory_component_id', trlKwf('Show'), 300))
            ->setDisplayField('title')
            ->setStoreUrl(Kwc_Admin::getInstance($class)->getControllerUrl('Directories').'/json-data');
    }
}
