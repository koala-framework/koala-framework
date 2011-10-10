<?php
class Kwc_Root_Category_GeneratorForm extends Kwf_Form
{
    private $_componentOrParent;
    public function __construct($componentOrParent, $generator)
    {
        $this->_componentOrParent = $componentOrParent;
        $this->setModel($generator->getModel());
        parent::__construct();
    }

    protected function _init()
    {
        parent::_init();

        $componentClasses = array();
        $componentNames = array();
        $component = $this->_componentOrParent;
        while (empty($componentClasses) && $component) {
            foreach (Kwc_Abstract::getSetting($component->componentClass, 'generators') as $key => $generator) {
                if (is_instance_of($generator['class'], 'Kwc_Root_Category_Generator')) {
                    foreach ($generator['component'] as $k => $class) {
                        $name = Kwc_Abstract::getSetting($class, 'componentName');
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
        $fields->add(new Kwf_Form_Field_TextField('name', trlKwf('Name of Page')))
            ->setAllowBlank(false);

        $fs = $fields->add(new Kwf_Form_Container_FieldSet('name', trlKwf('Name of Page')))
            ->setTitle(trlKwf('Custom Filename'))
            ->setCheckboxName('custom_filename')
            ->setCheckboxToggle(true);
        $fs->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');

        $fields->add(new Kwf_Form_Field_Select('component',  trlKwf('Pagetype')))
            ->setValues($componentNames)
            ->setPossibleComponentClasses($componentClasses) //just for PageController
            ->setTpl('<tpl for="."><div class="x-combo-list-item">{name}</div></tpl>')
            ->setAllowBlank(false);
        $fields->add(new Kwf_Form_Field_Checkbox('hide',  trlKwf('Hide in Menu')));
    }
}
