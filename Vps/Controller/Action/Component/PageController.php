<?php
class Vps_Controller_Action_Component_PageController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);

    private $_dynamicForms = array();

    protected function _hasPermissions($row, $action)
    {
        if ($row->getModel() instanceof Vps_Component_Model) {
            $component = $row->getData();
        } else {
            $component = Vps_Component_Data_Root::getInstance()
                ->getComponentById($row->parent_id, array('ignoreVisible' => true));
        }
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

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->parent_id = $this->_getParam('parent_id');
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
        //--- main generator form (if Category_Generator this contains Pagename and Pagetype)
        $componentOrParent = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getComponentOrParentId(), array('ignoreVisible' => true));
        if ($this->_getParam('id')) {
            if ($componentOrParent->componentId == 'root') {
                $this->_form = null;
            } else {
                $this->_form = $componentOrParent->generator->getPagePropertiesForm($componentOrParent);
            }
        } else {
            $gens = Vps_Component_Generator_Abstract::getInstances($componentOrParent, array('pageGenerator'=>true));
            if (count($gens)!=1) throw new Vps_Exception('pageGenerator not found');
            $gen = array_shift($gens);
            $this->_form = $gen->getPagePropertiesForm($componentOrParent);
        }

        if (!$this->_form) {
            $this->_form = new Vps_Form();
            $this->_form->setModel(Vps_Model_Abstract::getInstance('Vps_Component_Model'));
            $this->_form->add(new Vps_Form_Field_ShowField('name', trlVps('Name')));
        }

        //--- and
        $fields = $this->_form->fields;

        if (isset($fields['component'])) {
            $possibleComponentClasses = $fields['component']->getPossibleComponentClasses();
        } else {
            if (!$this->_getParam('id')) {
                throw new Vps_Exception("not supported for adding");
            }
            $possibleComponentClasses = array($componentOrParent->componentClass);
        }

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
            while ($page) {
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
                $page = $page->parent;
            }
        }
        unset($component);
        $generatorForms = array();
        $componentForms = array();
        $formsForComponent = array();
        foreach ($possibleComponentClasses as $key=>$componentClass) {
            $component = array(
                'componentClass' => $componentClass,
                'inheritClasses' => $inheritClasses
            );
            $formsForComponent[$key] = array();
            foreach (Vps_Component_Generator_Abstract::getInstances($component) as $g) {
                if ($g->getGeneratorFlag('page')) continue;
                if (!array_key_exists($g->getClass().'.'.$g->getGeneratorKey(), $generatorForms)) {
                    $f = $g->getPagePropertiesForm();
                    if ($f) {
                        $f->setName('gen_'.$g->getGeneratorKey());
                        $f->setIdTemplate('{0}-'.$g->getGeneratorKey());
                        $f->setCreateMissingRow(true);
                        $f->setShowDependingOnComponent(true);
                        $this->_dynamicForms[] = $f;
                        $fields->add($f);
                    }
                    $generatorForms[$g->getClass().'.'.$g->getGeneratorKey()] = $f;
                }
                if ($generatorForms[$g->getClass().'.'.$g->getGeneratorKey()]) {
                    $formsForComponent[$key][] = 'gen_'.$g->getGeneratorKey();
                }

                $classesToCheckForPagePropertiesForm = array('__pageComponent' => $componentClass);
                if ($g instanceof Vps_Component_Generator_Static) {
                    $classesToCheckForPagePropertiesForm = array_merge($classesToCheckForPagePropertiesForm, $g->getChildComponentClasses());
                }
                foreach ($classesToCheckForPagePropertiesForm as $childComponentKey=>$childComponentClass) {
                    if (!array_key_exists($childComponentKey.'_'.$childComponentClass, $componentForms)) {
                        $f = Vpc_Admin::getInstance($childComponentClass)->getPagePropertiesForm();
                        if ($f) {
                            $f->setName('cmp_'.$childComponentKey.'_'.$childComponentClass);
                            if ($childComponentKey=='__pageComponent') {
                                $f->setIdTemplate('{0}');
                            } else {
                                $f->setIdTemplate('{0}-'.$childComponentKey);
                            }
                            $f->setShowDependingOnComponent(true);
                            $this->_dynamicForms[] = $f;
                            $fields->add($f);
                        }
                        $componentForms[$childComponentKey.'_'.$childComponentClass] = $f;
                    }
                    if ($componentForms[$childComponentKey.'_'.$childComponentClass]) {
                        $formsForComponent[$key][] = 'cmp_'.$childComponentKey.'_'.$childComponentClass;
                    }
                }
            }
        }

        if (isset($fields['component'])) {
            $fields['component']->setFormsForComponent($formsForComponent);
        }
    }

    protected function _beforeValidate(array $postData)
    {
        parent::_beforeValidate($postData);
        //don't save hidden forms
        $cmpField = $this->_form->fields['component'];
        $component = $postData[$cmpField->getFieldName()];
        $formsForComponent = $cmpField->getFormsForComponent();
        $visibleForms = $formsForComponent[$component];
        foreach ($this->_dynamicForms as $f) {
            if (!in_array($f->getName(), $visibleForms)) {
                $this->_form->fields->remove($f);
            }
        }
    }
}
