<?php
class Vpc_Menu_Abstract_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        $this->_model = Vps_Model_Abstract::getInstance(Vpc_Abstract::getSetting($class, 'dataModel'));
        parent::__construct($name, $class);
        $cssClasses = Vpc_Abstract::getSetting($class, 'liCssClasses');
        if ($cssClasses) {
            $this->fields->add(new Vps_Form_Field_Select('cssClass', trlVps('Format of entry')))
                ->setValues($cssClasses)
                ->setShowNoSelection(true)
                ;
        }
    }
}
