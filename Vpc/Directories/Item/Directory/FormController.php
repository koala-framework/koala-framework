<?php
class Vpc_Directories_Item_Directory_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {
        $class = $this->_getParam('class');
        $this->_form->setModel(Vpc_Abstract::createChildModel($class));
        $this->_form->setIdTemplate(null);

        if (is_instance_of(
                Vpc_Abstract::getSetting($class, 'extConfig'),
                'Vpc_Directories_Item_Directory_ExtConfigTabs'
            ) || is_instance_of(
                Vpc_Abstract::getSetting($class, 'extConfigControllerIndex'),
                'Vpc_Directories_Item_Directory_ExtConfigTabs'
            ))
        {
            $this->_buttons['save'] = true;
        }

        $detailClasses = Vpc_Abstract::getChildComponentClasses($class, 'detail');
        $forms = array();
        foreach ($detailClasses as $key => $detailClass) {
            $form = Vpc_Abstract_Form::createComponentForm($detailClass, $class);
            $form->setIdTemplate('{0}');
            $form->setModel(Vpc_Abstract::createChildModel($class));
            $forms[$key] = $form;
        }

        if (count($forms) == 1) {
            $this->_form->add(reset($forms));
        } else {
            $cards = $this->_form->add(new Vps_Form_Container_Cards('component', trlVps('Type')))
                ->setDefaultValue(reset(array_keys($detailClasses)));
            $cards->getCombobox()
                ->setWidth(250)
                ->setListWidth(250)
                ->setAllowBlank(false);
            foreach ($forms as $key => $form) {
                $card = $cards->add();
                $card->add($form);
                $card->setTitle(Vpc_Abstract::getSetting($form->getClass(), 'componentName'));
                $card->setName($key);
                $card->setNamePrefix($key);
            }
            $cards->getCombobox()->getData()->cards = $cards->fields;
        }

        $classes = Vpc_Abstract::getChildComponentClasses($class);
        foreach ($classes as $class) {
            $formName = Vpc_Admin::getComponentClass($class, 'ItemEditForm');
            if ($formName) {
                $this->_form->add(new $formName($class, $class, $this->_getParam('componentId')));
            }
        }
    }

    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('componentId')) {
            if (isset($row->component_id)) $row->component_id = $this->_getParam('componentId');
            if (isset($row->visible)) $row->visible = 0;
        }
    }
}
