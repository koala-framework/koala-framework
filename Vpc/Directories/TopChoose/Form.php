<?php
class Vpc_Directories_TopChoose_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $showDirectoryClass = Vpc_Abstract::getSetting($class, 'showDirectoryClass');

        $directories = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass($showDirectoryClass, array('ignoreVisible' => true));

        $values = array();
        foreach ($directories as $directory) {
            $title = $directory->getTitle();
            $name = Vpc_Abstract::getSetting($directory->componentClass, 'componentName');
            if (strpos($name, '.') !== false) $name = substr(strrchr($name, '.'), 1);
            if ($title != $name) $title .= ' - ' . $name;
            $values[$directory->dbId] = $title;
        }

        $this->fields->add(new Vps_Form_Field_Select('directory_component_id', trlVps('Show')), 300)
            ->setValues($values);
    }
}
