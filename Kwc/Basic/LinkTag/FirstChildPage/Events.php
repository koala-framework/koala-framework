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
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($event->componentId);

        if ($component->parent && $component->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                $this->_class, $component->parent->dbId
            ));
        }
    }

    public function onPositionChanged(Kwf_Component_Event_Page_PositionChanged $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByDbId($event->dbId);
        foreach ($components as $component) {
            if ($component->row->pos == 1 && $component->parent &&
                $component->parent->componentClass == $this->_class
            ) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                    $this->_class, $component->parent->dbId
                ));
            }
        }
    }

    public function onPageAddedOrRemoved(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByDbId($event->dbId);
        foreach ($components as $component) {
            if ($component->parent && $component->parent->componentClass == $this->_class) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged(
                    $this->_class, $component->parent->dbId
                ));

                // copy from Kwc_Menu_Events
                $newCount = count($component->parent->getChildPages());
                if ($event instanceof Kwf_Component_Event_Page_Removed &&
                    $event->flag == Kwf_Component_Event_Page_Removed::FLAG_ROW_ADDED_REMOVED
                ) {
                    $newCount--;
                }
                $previousCount = $newCount;
                if ($event instanceof Kwf_Component_Event_Page_Added) {
                    $previousCount--;
                } else if ($event instanceof Kwf_Component_Event_Page_Removed) {
                    $previousCount++;
                }
                if (!$previousCount && $newCount || $previousCount && !$newCount) {
                    $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                        $this->_class, $component->parent->dbId
                    ));
                }
            }
        }
    }
}
