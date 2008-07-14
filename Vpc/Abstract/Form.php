<?php
class Vpc_Abstract_Form extends Vpc_Abstract_NonTableForm
{
    public function __construct($name, $class)
    {
        $this->setProperty('class', $class);
        $this->setTable(Vpc_Abstract::createTable($class));
        parent::__construct($name);
    }

    public function setComponentIdTemplate($idTemplate)
    {
        throw new Vps_Exception("deprecated, umbenannt in setIdTemplate");
    }

    public static function createComponentForm($idTemplate, $parentClass)
    {
        if ($parentClass) {
            $key = str_replace(array('{0}', '-', '_'), '', $idTemplate);
            $class = Vpc_Abstract::getChildComponentClass($parentClass, null, $key);
        } else {
            // TODO: aus dbid suchen
        }
        $f = Vpc_Admin::getComponentClass($class, 'Form');
        if (!$f) {
            throw new Vps_Exception("No Form for Component '$class' found");
        }
        $f->setIdTemplate($idTemplate);
        return new $f($name, $class);
    }
}
