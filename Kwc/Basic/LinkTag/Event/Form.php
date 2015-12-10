<?php
class Kwc_Basic_LinkTag_Event_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Kwf_Form_Field_Select('event_id', trlKwf('Event')))
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Kwc_Admin::getInstance($class)->getControllerUrl('Events').'/json-data'
            )
            ->setListWidth(210)
            ->setAllowBlank(false);
    }

    public function getIsCurrentLinkTag($parentRow)
    {
        $row = $this->getRow($parentRow);
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId('events_'.$row->event_id, array('ignoreVisible'=>true));
        if ($c) {
            return 'event_'.$c->parent->dbId == $this->getName();
        } else {
            return false;
        }
    }
}
