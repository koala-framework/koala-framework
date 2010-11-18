<?php
class Vpc_Basic_LinkTag_ComponentClass_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
    }
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_Select('target_component_id', trlVps('Page')))
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Vpc_Admin::getInstance($class)->getControllerUrl('Components').'/json-data'
            )
            ->setWidth(300)
            ->setListWidth(410);
    }
}
