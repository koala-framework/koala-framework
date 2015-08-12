<?php
class Kwc_Abstract_Composite_Form extends Kwc_Abstract_Form
{
    protected $_createFieldsets = true;

    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        if (!$this->getModel()) {
            $this->setModel(new Kwf_Model_FnF());
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
        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        if (!isset($generators['child'])) return;
        $classes = $generators['child']['component'];
        foreach ($classes as $key => $class) {
            if (!$class) continue;
            $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), "-$key", $key);
            if ($form && count($form->fields)) {
                if ($this->_getIdTemplateForChild($key)) {
                    $form->setIdTemplate($this->_getIdTemplateForChild($key));
                }
                if (!$this->_createFieldsets || !Kwc_Abstract::hasSetting($class, 'componentName')) {
                    $this->add($form);
                } else {
                    $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($class, 'componentName'));
                    $name = str_replace('.', ' ', $name);
                    $this->add(new Kwf_Form_Container_FieldSet($name))
                        ->setName($key)
                        ->add($form);
                }
            }
        }
    }
}
