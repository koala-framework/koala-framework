<?php
class Kwc_Advanced_Sitemap_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => array(
                'Kwf_Component_Event_Page_Removed', 'Kwf_Component_Event_Page_Added',
                'Kwf_Component_Event_Component_RecursiveRemoved', 'Kwf_Component_Event_Component_RecursiveAdded',
                'Kwf_Component_Event_Page_RecursiveUrlChanged'
            ),
            'callback' => 'onPageRemovedOrAdded'
        );

        return $ret;
    }

    public function onPageRemovedOrAdded(Kwf_Events_Event_Abstract $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class, $event->component));
    }
}
