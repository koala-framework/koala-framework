<?php
class Vpc_Abstract_Form extends Vps_Form
{
    public function __construct($name, $class, $id = null)
    {
        $this->setProperty('class', $class);
        $this->setTable(Vpc_Abstract::createTable($class));
        parent::__construct($name, $id);
    }

    public function setComponentIdTemplate($idTemplate)
    {
        throw new Vps_Exception("deprecated, umbenannt in setIdTemplate");
    }

    public static function createComponentForm($name, $class)
    {
        $f = Vpc_Admin::getComponentClass($class, 'Form');
        if (!$f) {
            throw new Vps_Exception("No Form for Component '$class' found");
        }
        return new $f($name, $class);
    }
}
