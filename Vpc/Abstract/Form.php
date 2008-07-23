<?php
class Vpc_Abstract_Form extends Vps_Form
{
    public function __construct($name, $class)
    {
        $this->setClass($class);
        $this->setTable(Vpc_Abstract::createTable($class));
        parent::__construct($name, $class);
    }
    
    public static function createComponentFormByDbIdTemplate($dbIdTemplate, $name = null)
    {
        $componentClass = null;
        $dbIdShortcut = $dbIdTemplate;
        $id = null;
        if ($pos = strpos($dbIdTemplate, '{0}')) {
            $dbIdShortcut = substr($dbIdTemplate, 0, strpos($dbIdTemplate, '{0}'));
            $id = substr($dbIdTemplate, strpos($dbIdTemplate, '{0}') + 4);
        }
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $g) {
                if (isset($g['dbIdShortcut']) && $g['dbIdShortcut'] == $dbIdShortcut) {
                    $componentClass = $g['component'];
                }
            }
        }
        if (!$componentClass) {
            throw new Vps_Exception("No component for dbIdShortcut '$dbIdShortcut' found.");
        }
        if ($id) { // id hatte form 'dbId_{0}-key', also für Key Unterkomponente suchen
            $form = self::createChildComponentForm($componentClass, $id, $name);
            $form->setIdTemplate($dbIdTemplate);
        } else {
            $form = self::createComponentForm($componentClass, $name);
            $form->setIdTemplate($dbIdShortcut . '{0}');
        }
        return $form;
    }
    
    public static function createComponentForm($componentClass, $name = null)
    {
        // Es wurde ein dbIdTemplate angegeben
        if (!in_array($componentClass, Vpc_Abstract::getComponentClasses())) {
            return self::createComponentFormByDbIdTemplate($componentClass);
        }
        
        $formClass = Vpc_Admin::getComponentClass($componentClass, 'Form');
        if (!$formClass) {
            throw new Vps_Exception("No Form for Component '$componentClass' found");
        }
        if (!$name) $name = $componentClass;
        return new $formClass($name, $componentClass);
    }
    
    public static function createChildComponentForm($componentClass, $id, $name = null)
    {
        $id = str_replace('-', '', $id);
        $idTemplate = '{0}';
        $childComponentClass = null;
        
        foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $generatorKey => $generatorData) {
            $generator = Vps_Component_Generator_Abstract::getInstance($componentClass, $generatorKey);
            $component = $generator->getComponentByKey($id);
            if ($component) {
                $childComponentClass = $component;
                $idTemplate .= $generator->getIdSeparator() . $id;
            }
        }
        if (!$childComponentClass) {
            throw new Vps_Exception("No child component with id '$id' for '$componentClass' found.");
        }
        $form = self::createComponentForm($childComponentClass, $name);
        $form->setIdTemplate($idTemplate);
        return $form;        
    }
}
