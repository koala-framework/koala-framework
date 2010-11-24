<?php
class Vpc_Abstract_Form extends Vps_Form
{
    public function __construct($name, $class)
    {
        $this->setClass($class);
        if ($class) {
            if (!$this->getModel()) {
                $model = Vpc_Abstract::createOwnModel($class);
                if ($model) $this->setModel($model);
            }
            $this->setCreateMissingRow(true);
        }
        parent::__construct($name);
    }

    /**
     * @return Vpc_Abstract_Form
     **/
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
            throw new Vpc_Exception("No component for dbIdShortcut '$dbIdShortcut' found.");
        }
        if (is_array($componentClass)) {
            if (sizeof($componentClass) > 1) {
                throw new Vpc_Exception("Can't have multiple component classes");
            }
            reset($componentClass);
            $componentClass = current($componentClass);
        }

        if ($id) { // id hatte form 'dbId_{0}-key', also für Key Unterkomponente suchen
            $form = self::createChildComponentForm($componentClass, $id, $name);
            if ($form) $form->setIdTemplate($dbIdTemplate);
        } else {
            $form = self::createComponentForm($componentClass, $name);
            if ($form) $form->setIdTemplate($dbIdShortcut . '{0}');
        }
        return $form;
    }
    /**
     * @return Vpc_Abstract_Form
     **/
    public static function createComponentForm($componentClass, $name = null)
    {
        // Es wurde ein dbIdTemplate angegeben
        if (!in_array($componentClass, Vpc_Abstract::getComponentClasses())) {
            return self::createComponentFormByDbIdTemplate($componentClass);
        }

        $formClass = Vpc_Admin::getComponentClass($componentClass, 'Form');
        if (!$formClass || $formClass == 'Vpc_Abstract_Form') return null;

        if (!$name) $name = $componentClass;
        $form = new $formClass($name, $componentClass);
        if ($form instanceof Vpc_Abstract_FormEmpty) { return null; }
        return $form;
    }

    /**
     * @return Vpc_Abstract_Form
     **/
    public static function createChildComponentForm($componentClass, $id, $name = null)
    {
        if (substr($id, 0, 1)=='-' || substr($id, 0, 1)=='_') {
            $id = substr($id, 1);
        }
        if (!$name) $name = $id;
        $idTemplate = '{0}';
        $childComponentClass = null;

        //TODO: wenns mal benötigt wird recursiv die id weiter nach unten gehen und komponenten suchen

        foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $generatorKey => $generatorData) {
            $generator = Vps_Component_Generator_Abstract::getInstance($componentClass, $generatorKey);
            if ($childComponentClass = $generator->getComponentByKey($id)) {
                $idTemplate .= $generator->getIdSeparator() . $id;
                break;
            }
        }
        if (!$childComponentClass) {
            throw new Vpc_Exception("No child component with id '$id' for '$componentClass' found.");
        }
        $form = self::createComponentForm($childComponentClass, $name);
        if ($form) {
            $form->setIdTemplate($idTemplate);
        }
        return $form;
    }
}
