<?php
class Vpc_Basic_LinkTag_Event_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_Select('event_id', trlVps('Event')))
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Vpc_Admin::getInstance($class)->getControllerUrl('Events').'/json-data'
            )
            ->setListWidth(210)
            ->setAllowBlank(false);
    }

    public function getIsCurrentLinkTag($parentRow)
    {
        $row = $this->getRow($parentRow);
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId('events_'.$row->event_id, array('ignoreVisible'=>true));
        return 'event_'.$c->parent->dbId == $this->getName();
    }
}
