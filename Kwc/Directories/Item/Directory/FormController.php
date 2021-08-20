<?php
class Kwc_Directories_Item_Directory_FormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {
        $class = $this->_getParam('class');
        $this->_form->setModel(Kwc_Abstract::createChildModel($class));
        $this->_form->setIdTemplate(null);

        if (is_instance_of(
                Kwc_Abstract::getSetting($class, 'extConfig'),
                'Kwc_Directories_Item_Directory_ExtConfigTabs'
            ) || is_instance_of(
                Kwc_Abstract::getSetting($class, 'extConfigControllerIndex'),
                'Kwc_Directories_Item_Directory_ExtConfigTabs'
            ))
        {
            $this->_buttons['save'] = true;
        }

        $detailClasses = Kwc_Abstract::getChildComponentClasses($class, 'detail');
        $forms = array();
        foreach ($detailClasses as $key => $detailClass) {
            $formClass = Kwc_Admin::getComponentClass($detailClass, 'Form');
            $form = new $formClass($key, $detailClass, $class);
            $form->setIdTemplate('{0}');
            $form->setModel(Kwc_Abstract::createChildModel($class));
            $forms[$key] = $form;
        }

        if (count($forms) == 1) {
            $this->_form->add(reset($forms));
        } else {
            $keys = array_keys($detailClasses);
            $cards = $this->_form->add(new Kwf_Form_Container_Cards('component', trlKwf('Type')))
                ->setDefaultValue(reset($keys));
            $cards->getCombobox()
                ->setWidth(250)
                ->setListWidth(250)
                ->setAllowBlank(false);
            foreach ($forms as $key => $form) {
                $card = $cards->add();
                $card->add($form);
                $card->setTitle(Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($form->getClass(), 'componentName')));
                $card->setName($key);
                $card->setNamePrefix($key);
            }
            $cards->getCombobox()->getData()->cards = $cards->fields;
        }

        $classes = Kwc_Abstract::getChildComponentClasses($class);
        foreach ($classes as $class) {
            $formName = Kwc_Admin::getComponentClass($class, 'ItemEditForm');
            if ($formName) {
                $this->_form->add(new $formName('detail', $class, $this->_getParam('componentId')));
            }
        }
    }

    protected function _getUserActionsLogConfig()
    {
        $ret = parent::_getUserActionsLogConfig();
        $generator = Kwf_Component_Generator_Abstract::getInstance($this->_getParam('class'), 'detail');
        if ($generator) {
            $ret['componentId'] = $this->_getParam('componentId') . $generator->getIdSeparator() . $this->_getParam('id');
        }
        return $ret;
    }

    protected function _hasPermissions($row, $action)
    {
        if (isset($row->component_id) && $row->component_id != $this->_getParam('componentId')) {
            return false;
        }
        return parent::_hasPermissions($row, $action);
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('componentId')) {
            if (isset($row->component_id)) $row->component_id = $this->_getParam('componentId');
            if (isset($row->visible)) $row->visible = 0;
        }
    }
}
