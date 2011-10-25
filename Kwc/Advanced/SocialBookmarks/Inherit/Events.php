<?php
class Kwc_Advanced_SocialBookmarks_Inherit_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $componentClasses = Kwc_Abstract::getComponentClassesByParentClass(
            'Kwc_Advanced_SocialBookmarks_Component'
        );
        foreach ($componentClasses as $componentClass) {
            $ret[] = array(
                'class' => $componentClass,
                'event' => 'Kwf_Component_Event_Component_ContentChanged',
                'callback' => 'onParentContentChanged'
            );
        }
        return $ret;
    }

    public function onParentContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->dbId . '-child'
        ));
    }
}
