<?php
class Vpc_Abstract_Composite_Form extends Vpc_Abstract_Form
{
    protected $_createFieldsets = true;

    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        if (!$this->getModel()) {
            $this->setModel(new Vps_Model_FnF());
            $this->setCreateMissingRow(true);
        }
    }

    protected function _getIdTemplateForChild($key)
    {
        return null;
    }

    protected function _initFields()
    {
        parent::_initFields();
        $this->setCreateMissingRow(true);

        if (!$this->getClass()) return;
        $generators = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $generators['child']['component'];
        foreach ($classes as $key => $class) {
            if (!$class) continue;
            $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-$key", $key);
            if ($form && count($form->fields)) {
                if ($this->_getIdTemplateForChild($key)) {
                    $form->setIdTemplate($this->_getIdTemplateForChild($key));
                }
                if (!$this->_createFieldsets || !Vpc_Abstract::hasSetting($class, 'componentName')) {
                    $this->add($form);
                } else {
                    $name = Vpc_Abstract::getSetting($class, 'componentName');
                    $name = str_replace('.', ' ', $name);
                    $this->add(new Vps_Form_Container_FieldSet($name))
                        ->setName($key)
                        ->add($form);
                }
            }
        }
    }
}
