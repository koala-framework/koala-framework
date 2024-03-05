<?php
class Kwc_Basic_LinkTag_Event_Form extends Kwc_Basic_LinkTag_News_Form
{
    protected function _createFilteredField()
    {
        $events = new Kwf_Form_Field_Select('event_id', trlKwf('Event'));
        $events
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Kwc_Admin::getInstance($this->getClass())->getControllerUrl('Events').'/json-data'
            )
            ->setListWidth(210)
            ->setAllowBlank(false);
        return $events;
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
