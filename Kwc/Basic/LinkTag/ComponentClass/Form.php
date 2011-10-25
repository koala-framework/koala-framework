<?php
class Kwc_Basic_LinkTag_ComponentClass_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
    }
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Kwf_Form_Field_Select('target_component_id', trlKwf('Page')))
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Kwc_Admin::getInstance($class)->getControllerUrl('Components').'/json-data'
            )
            ->setWidth(300)
            ->setListWidth(410);
    }
}
