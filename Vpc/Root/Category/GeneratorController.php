<?php

class Vpc_Root_Category_GeneratorController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);
    protected $_modelName = 'Vpc_Root_Category_GeneratorModel';

    protected function _hasPermissions($row, $action)
    {
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentById($row->parent_id, array('ignoreVisible' => true));
        $ret = false;
        while ($component) {
            if ($component->componentId == $this->_getParam('componentId')) $ret = true;
            $component = $component->parent;
        }
        if ($ret) {
            return parent::_hasPermissions($row, $action);
        } else {
            return false;
        }
    }

    private function _getComponentOrParentId()
    {
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
        $componentNames = array();
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getComponentOrParentId(), array('ignoreVisible' => true));
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
            ->setValues($componentNames)
            ->setTpl('<tpl for="."><div class="x-combo-list-item">{name}</div></tpl>')
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Checkbox('hide',  trlVps('Hide in Menu')));

        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getComponentOrParentId(), array('ignoreVisible' => true));
        if ($this->_getParam('id')) {
            //$component is the component, just get inheritClasses
            $inheritClasses = $component->inheritClasses;
        } else {
            //$component is the parent component (we are adding)
            //this code is very similar to Vps_Component_Data::__get inheritClasses
            $inheritClasses = array();
            $page = $component;
            while (($page = $page->parent)) {
                foreach (Vpc_Abstract::getSetting($page->componentClass, 'generators') as $gKey=> $g) {
                    if (isset($g['inherit']) && $g['inherit']) {
                        if (isset($g['unique']) && $g['unique']) continue; //ignore, not edited
                        if (!in_array($page->componentClass, $inheritClasses)) {
                            $inheritClasses[] = $page->componentClass;
                        }
                    }
                }
                if ($page->inherits) {
                    //wenn page selbst erbt einfach von da übernehmen (rekursiver aufruf)
                    $inheritClasses = array_merge($inheritClasses, $page->inheritClasses);
                    break; //aufhören, rest kommt durch rekursion daher
                }
            }
        }
        unset($component);
        $generators = array();
        $generatorForms = array();
        $formsForComponent = array();
        foreach ($componentClasses as $key=>$componentClass) {
            $component = array(
                'componentClass' => $componentClass,
                'inheritClasses' => $inheritClasses
            );
            $formsForComponent[$key] = array();
            foreach (Vps_Component_Generator_Abstract::getInstances($component) as $g) {
                if (!isset($generators[$g->getClass().'.'.$g->getGeneratorKey()])) {
                    $generators[$g->getClass().'.'.$g->getGeneratorKey()] = $g;
                    $f = $g->getPagePropertiesForm();
                    if ($f) {
                        $f->setName('gen_'.$g->getGeneratorKey());
                        $f->setIdTemplate('{0}-'.$g->getGeneratorKey());
                        $f->setCreateMissingRow(true);
                        $f->setShowDependingOnComponent(true);
                        $fields->add($f);
                    }
                    $generatorForms[$g->getClass().'.'.$g->getGeneratorKey()] = $f;
                }
                if ($generatorForms[$g->getClass().'.'.$g->getGeneratorKey()]) {
                    $formsForComponent[$key][] = 'gen_'.$g->getGeneratorKey();
                }
            }
        }
        $fields['component']->setFormsForComponent($formsForComponent);

        /*
        foreach ($componentClasses as $key=>$componentClass) {
            $f = Vpc_Admin::getInstance($componentClass)->getPagePropertiesForm();
            if ($f) {
                $f->setName($key);
                $f->setIdTemplate('{0}');
                $f->setCreateMissingRow(true);
                $fields->add($f);
            }
        }
        + the same for child components (using editComponents?)
        */
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
    }
}
