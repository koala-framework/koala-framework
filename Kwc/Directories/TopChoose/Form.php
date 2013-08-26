<?php
class Kwc_Directories_TopChoose_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $showDirectoryClass = Kwc_Abstract::getSetting($class, 'showDirectoryClass');

        $directories = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass($showDirectoryClass, array('ignoreVisible' => true));

        $values = array();
        foreach ($directories as $directory) {
            $title = $directory->getTitle();
            if (Kwc_Abstract::hasSetting($class, 'componentNameShort')) {
                $name = Kwc_Abstract::getSetting($class, 'componentNameShort');
            } else {
                $name = Kwc_Abstract::getSetting($class, 'componentName');
            }
            $name = Kwf_Trl::getInstance()->trlStaticExecute($name);
            if ($title != $name) $title .= ' - ' . $name;
            $values[$directory->dbId] = $title;
        }

        $this->fields->add(new Kwf_Form_Field_Select('directory_component_id', trlKwf('Show')), 300)
            ->setValues($values);
    }
}
