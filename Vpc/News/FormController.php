<?php
class Vpc_News_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save' => true, 'add' => true);

    public function preDispatch()
    {
        $tablename = Vpc_Abstract::getSetting($this->class, 'tablename');
        $this->_table = new $tablename(array('componentClass'=>$this->class));
        parent::preDispatch();
    }

    public function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->_form->add(new Vps_Form_Field_TextArea('teaser', trlVps('Teaser')))
            ->setWidth(300)
            ->setHeight(100);
        $this->_form->add(new Vps_Form_Field_DateField('publish_date', trlVps('Publish Date')))
            ->setAllowBlank(false);
        $this->_form->add(new Vps_Form_Field_DateField('expiry_date', trlVps('Expiry Date')));


        $component = get_class($this);
        $component = substr($component, 0, strrpos($component, '_')) . '_Component';
        $component = Vps_PageCollection_Abstract::getInstance()->getComponentByParentClass($component);
        $componentName = get_class($component);

        $childComponentClasses = Vpc_Abstract::getSetting($componentName, 'childComponentClasses');
        $categories = Vpc_Abstract::getSetting($componentName, 'categories');

        if ($categories) {
            foreach ($categories as $cKey => $category) {
                $formName = Vpc_Admin::getComponentFile(
                    $childComponentClasses[$cKey], 'Form', 'php', true
                );
                if ($formName) {
                    $this->_form->add(new $formName($childComponentClasses[$cKey]))
                        ->setBaseCls('x-plain');
                }

            }
        }
    }

    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('component_id')) {
            $row->component_id = $this->_getParam('component_id');
            $row->visible = 0;
        }
    }
}
