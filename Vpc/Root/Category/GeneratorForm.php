<?php
class Vpc_Root_Category_GeneratorForm extends Vps_Form
{
    private $_componentOrParent;
    public function __construct($componentOrParent)
    {
        $this->_componentOrParent = $componentOrParent;
        $this->setModel($componentOrParent->generator->getModel());
        parent::__construct();
    }

    protected function _init()
    {
        parent::_init();

        $componentClasses = array();
        $componentNames = array();
        $component = $this->_componentOrParent;
        while (empty($componentClasses) && $component) {
            foreach (Vpc_Abstract::getSetting($component->componentClass, 'generators') as $key => $generator) {
                if (is_instance_of($generator['class'], 'Vpc_Root_Category_Generator')) {
                    foreach ($generator['component'] as $k => $class) {
                        $name = Vpc_Abstract::getSetting($class, 'componentName');
                        if ($name) {
                            $name = str_replace('.', ' ', $name);
                            $componentNames[$k] = $name;
                            $componentClasses[$k] = $class;
                        }
                    }
                }
            }
            $component = $component->parent;
        }

        $fields = $this->fields;
        $fields->add(new Vps_Form_Field_TextField('name', trlVps('Name of Page')))
            ->setAllowBlank(false);

        $fs = $fields->add(new Vps_Form_Container_FieldSet('name', trlVps('Name of Page')))
            ->setTitle(trlVps('Custom Filename'))
            ->setCheckboxName('custom_filename')
            ->setCheckboxToggle(true);
        $fs->add(new Vps_Form_Field_TextField('filename', trlVps('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');

        $fields->add(new Vps_Form_Field_Select('component',  trlVps('Pagetype')))
            ->setValues($componentNames)
            ->setPossibleComponentClasses($componentClasses) //just for PageController
            ->setTpl('<tpl for="."><div class="x-combo-list-item">{name}</div></tpl>')
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Checkbox('hide',  trlVps('Hide in Menu')));
    }
}
