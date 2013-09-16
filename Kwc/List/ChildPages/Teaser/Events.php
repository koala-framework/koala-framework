<?php
class Kwc_List_ChildPages_Teaser_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $g = Kwc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $g['child']['component'],
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onPageAddedOrRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onPageAddedOrRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_PositionChanged',
            'callback' => 'onPageAddedOrRemoved'
        );
        return $ret;
    }

    public function onChildHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $ev)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $ev->component->parent));
    }

    public function onPageAddedOrRemoved(Kwf_Component_Event_Component_Abstract $ev)
    {
        // deleting by component would cause on every page change searching for ChildPages_Teaser_Component, this is faster and sufficent
        if ($ev->component->generator instanceof Kwc_Root_Category_Generator) {
            $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class, $ev->component));
        }
    }
}
