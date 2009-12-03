<?php

class Vps_Controller_Action_Component_PageEditController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);
    protected $_tableName = 'Vps_Dao_Pages';

    public function _isAllowed($user)
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getComponentId(), array('ignoreVisible'=>true));

        if (!$c) {
            throw new Vps_Exception("Can't find component to check permissions");
        }
        return Vps_Registry::get('acl')->getComponentAcl()
            ->isAllowed($this->_getAuthData(), $c);
        return true;
    }

    private function _getComponentId()
    {
        $id = null;
        if ($this->_getParam('id')) {
            $id = $this->_getParam('id');
        } else {
            $id = $this->_getParam('parent_id');
        }
        return $id;
    }

    protected function _initFields()
    {
        $componentClasses = array();
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getComponentId(), array('ignoreVisible' => true));
        while (empty($componentClasses) && $component) {
            foreach (Vpc_Abstract::getSetting($component->componentClass, 'generators') as $key => $generator) {
                if (is_instance_of($generator['class'], 'Vps_Component_Generator_Page')) {
                    foreach ($generator['component'] as $k => $class) {
                        $name = Vpc_Abstract::getSetting($class, 'componentName');
                        if ($name) {
                            $name = str_replace('.', ' ', $name);
                            $componentClasses[$k] = $name;
                        }
                    }
                }
            }
            $component = $component->parent;
        }

        $fields = $this->_form->fields;
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
            ->setValues($componentClasses)
            ->setTpl('<tpl for="."><div class="x-combo-list-item">{name}</div></tpl>')
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Checkbox('hide',  trlVps('Hide in Menu')));
        $fields->add(new Vps_Form_Field_TextField('tags', trlVps('Tags')));
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
    }
}
