<?php
class Vpc_Basic_LinkTag_Event_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
    }
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_Select('event_id', trlVps('Event')))
            ->setDisplayField('title')
            ->setStoreUrl(
                Vpc_Admin::getInstance($class)->getControllerUrl('Events').'/json-data'
            )
            ->setListWidth(210);
    }
}
