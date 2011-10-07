<?php
class Vpc_Advanced_SocialBookmarks_Inherit_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $componentClasses = Vpc_Abstract::getComponentClassesByParentClass(
            'Vpc_Advanced_SocialBookmarks_Component'
        );
        foreach ($componentClasses as $componentClass) {
            $ret[] = array(
                'class' => $componentClass,
                'event' => 'Vps_Component_Event_Component_ContentChanged',
                'callback' => 'onParentContentChanged'
            );
        }
        return $ret;
    }

    public function onParentContentChanged(Vps_Component_Event_Component_ContentChanged $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->dbId . '-child'
        ));
    }
}
