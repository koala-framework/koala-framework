<?php
class Vpc_Abstract_Form extends Vps_Form
{
    public function __construct($name, $class)
    {
        $this->setTable(Vpc_Abstract::createTable($class));
        parent::__construct($name, $class);
    }

    public function setComponentIdTemplate($idTemplate)
    {
        throw new Vps_Exception("deprecated, umbenannt in setIdTemplate");
    }

    public static function createComponentForm($idTemplate, $parentClass = null)
    {
        if ($parentClass) {
            $key = str_replace(array('{0}', '-', '_'), '', $idTemplate);
            $class = Vpc_Abstract::getChildComponentClass($parentClass, null, $key);
        } else {
            throw new Vps_Exception('TODO');
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
