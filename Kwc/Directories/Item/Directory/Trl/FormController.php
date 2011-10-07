<?php
class Kwc_Directories_Item_Directory_Trl_FormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');

    public function preDispatch()
    {
        parent::preDispatch();
                                                            //idSeparator dynam. holen?
        $this->_form->setId($this->_getParam('componentId').'_'.$this->_getParam('id'));
        $this->_form->setCreateMissingRow(true);
    }

    public function _initFields()
    {
        $this->_form = Kwc_Abstract_Form::createChildComponentForm(
                $this->_getParam('class'), '-detail', $this->_getParam('class'));
        $this->_form->setIdTemplate(null);

        $this->_form->setModel(Kwc_Abstract::createChildModel($this->_getParam('class')));

        $classes = Kwc_Abstract::getChildComponentClasses($this->_getParam('class'));
        foreach ($classes as $class) {
            $formName = Kwc_Admin::getComponentClass($class, 'ItemEditForm');
            if ($formName) {
                $this->_form->add(new $formName($class, $class));
            }
        }
    }
/*
    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('componentId')) {
            if (isset($row->component_id)) $row->component_id = $this->_getParam('componentId');
            if (isset($row->visible)) $row->visible = 0;
        }
    }*/
}
