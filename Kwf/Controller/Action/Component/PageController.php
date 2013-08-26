<?php
class Kwf_Controller_Action_Component_PageController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);

    private $_dynamicForms = array();

    private $_componentField;

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
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
        $componentOrParent = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getComponentOrParentId(), array('ignoreVisible' => true));
        if (!$componentOrParent ||
            !Kwf_Registry::get('acl')->getComponentAcl()
                ->isAllowed($this->_getAuthData(), $componentOrParent
        )) {
            throw new Kwf_Exception_AccessDenied();
        }
        if ($this->_getParam('id')) {
            if ($componentOrParent->componentId == 'root') {
                $this->_form = null;
            } else {
                $this->_form = $componentOrParent->generator->getPagePropertiesForm($componentOrParent);
            }
        } else {
            $gens = Kwf_Component_Generator_Abstract::getInstances($componentOrParent, array('pageGenerator'=>true));
            if (count($gens)!=1) throw new Kwf_Exception('pageGenerator not found');
            $gen = array_shift($gens);
            $this->_form = $gen->getPagePropertiesForm($componentOrParent);
        }

        if (!$this->_form) {
            $this->_form = new Kwf_Form();
            $this->_form->setModel(Kwf_Model_Abstract::getInstance('Kwf_Component_Model'));
            $this->_form->add(new Kwf_Form_Field_ShowField('name', trlKwf('Name')));
        }

        //--- and now add the more complicated additional forms
        $fields = $this->_form->fields;

        if (isset($fields['component'])) {
            $possibleComponentClasses = $fields['component']->getPossibleComponentClasses();
            $this->_componentField = $fields['component'];
        } else {
            if (!$this->_getParam('id')) {
                throw new Kwf_Exception("not supported for adding");
            }
            $possibleComponentClasses = array($componentOrParent->componentClass);
        }

        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getComponentOrParentId(), array('ignoreVisible' => true));
        if ($this->_getParam('id')) {
            //$component is the component, just get inheritClasses
            $inheritClasses = $component->inheritClasses;
        } else {
            //$component is the parent component (we are adding)
            //this code is very similar to Kwf_Component_Data::__get inheritClasses
            $inheritClasses = array();
            $page = $component;
            while ($page) {
                foreach (Kwc_Abstract::getSetting($page->componentClass, 'generators') as $gKey=> $g) {
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
            $classesToCheckForPagePropertiesForm = array('__pageComponent' => $componentClass);
            foreach (Kwf_Component_Generator_Abstract::getInstances($component) as $g) {
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

                if ($g instanceof Kwf_Component_Generator_Static) {
                    $classesToCheckForPagePropertiesForm = array_merge($classesToCheckForPagePropertiesForm, $g->getChildComponentClasses());
                }
            }
            foreach ($classesToCheckForPagePropertiesForm as $childComponentKey=>$childComponentClass) {
                if (!array_key_exists($key.'_'.$childComponentKey, $componentForms)) {
                    $config = array();
                    $config['component'] = $componentOrParent;
                    if ($this->_getParam('id')) {
                        $config['mode'] = 'edit';
                    } else {
                        $config['mode'] = 'add';
                    }
                    $f = Kwc_Admin::getInstance($childComponentClass)->getPagePropertiesForm($config);
                    if ($f) {
                        $f->setName('cmp_'.$key.'_'.$childComponentKey);
                        if ($childComponentKey=='__pageComponent') {
                            $f->setIdTemplate('{0}');
                        } else {
                            $f->setIdTemplate('{0}-'.$childComponentKey);
                        }
                        $f->setShowDependingOnComponent(true);
                        $this->_dynamicForms[] = $f;
                        $fields->add($f);
                    }
                    $componentForms[$key.'_'.$childComponentKey] = $f;
                }
                if ($componentForms[$key.'_'.$childComponentKey]) {
                    $formsForComponent[$key][] = 'cmp_'.$key.'_'.$childComponentKey;
                }
            }
        }

        if (isset($fields['component'])) {
            $fields['component']->setFormsForComponent($formsForComponent);
        }

        $this->_form->setId($this->_getParam('id'));
    }

    protected function _beforeValidate(array $postData)
    {
        parent::_beforeValidate($postData);
        //don't save hidden forms
        if ($this->_componentField) {
            $component = $postData[$this->_componentField->getFieldName()];
            $formsForComponent = $this->_componentField->getFormsForComponent();
            $visibleForms = $formsForComponent[$component];
            foreach ($this->_dynamicForms as $f) {
                if (!in_array($f->getName(), $visibleForms)) {
                    $this->_form->fields->remove($f);
                }
            }
        }
    }
}
