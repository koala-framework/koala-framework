<?php
class Kwc_Directories_Category_ShowCategories_Form extends Kwc_Abstract_Composite_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->setModel(new Kwf_Model_FnF(array('columns' => array('id', 'component_id'))));
    }

    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Categories')))
            ->setWidth(500);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Directories_Category_ShowCategories_Model');
        $mf = $fs->add(new Kwf_Form_Field_MultiFields($model, 'foo'))
            ->setReferences(array(
                'columns' => array('component_id'),
                'refColumns' => array('id')
            ))
            ->setMinEntries(0);
        $select = new Kwf_Form_Field_Select('category_id', trlKwf('Show Category'));
        $select
            ->setValues(
                Kwc_Admin::getInstance($this->getClass())->getControllerUrl('Directories').'/json-data'
            )
            ->setAllowBlank(false)
            ->setWidth(300);
        $mf->fields->add($select);
    }
}
