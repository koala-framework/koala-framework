<?php
class Kwc_Basic_LinkTag_FirstChildPage_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onRecursiveUrlChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_PositionChanged',
            'callback' => 'onPositionChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onPageAddedOrRemoved'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onPageAddedOrRemoved'
        );
        return $ret;
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $component = $event->component->parent;

        while($component && is_instance_of($component->componentClass, 'Kwc_Basic_LinkTag_FirstChildPage_Component')) {
            if ($component->componentClass == $this->_class) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                    $this->_class, $component
                ));
            }
            $component = $component->parent;
        }
    }

    public function onPositionChanged(Kwf_Component_Event_Page_PositionChanged $event)
    {
        $component = $event->component;
        if (isset($component->chained)) {
            $pos = $component->chained->row->pos;
        } else {
            $pos = $component->row->pos;
        }
        if ($pos == 1 && $component->parent &&
            $component->parent->componentClass == $this->_class
        ) {
            $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                $this->_class, $component->parent
            ));
        }
    }

    public function onPageAddedOrRemoved(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        $component = $event->component;
        if ($component->parent && $component->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                $this->_class, $component->parent
            ));
        }
    }
}
