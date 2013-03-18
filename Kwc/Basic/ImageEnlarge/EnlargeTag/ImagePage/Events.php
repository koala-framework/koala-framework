<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        //find components that can create ourself ($this->_class)
        foreach ($this->_getCreatingClasses($this->_class, 'Kwc_Abstract_Image_Component') as $class) {

            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_Media_Changed',
                'callback' => 'onMediaChanged'
            );
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_Component_ContentChanged',
                'callback' => 'onContentChanged'
            );
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                'callback' => 'onClassContentChanged'
            );

            $imageEnlargeClasses = array();
            foreach ($this->_getCreatingClasses($class) as $c) {
                if (in_array('Kwc_Basic_LinkTag_Component', Kwc_Abstract::getParentClasses($c))) {
                    $imageEnlargeClasses = $this->_getCreatingClasses($c);
                } else {
                    $imageEnlargeClasses = array($c);
                }
            }
            foreach ($imageEnlargeClasses as $imageEnlargeClass) {
                // TODO: does not cover "List_Switch with ImageEnlarge as large component (we have to go up one more level)"
                foreach ($this->_getCreatingClasses($imageEnlargeClass, 'Kwc_Abstract_List_Component') as $c) {
                    $ret[] = array(
                        'class' => $c,
                        'event' => 'Kwc_Abstract_List_EventItemDeleted',
                        'callback' => 'onListItemChange'
                    );
                    $ret[] = array(
                        'class' => $c,
                        'event' => 'Kwc_Abstract_List_EventItemInserted',
                        'callback' => 'onListItemChange'
                    );
                }
            }
        }
        return $ret;
    }

    public function onListItemChange(Kwf_Component_Event_Component_Abstract $event)
    {
        // get list item
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($event->dbId, array('ignoreVisible' => true));
        // get previous and next items and delete the cache for them
        $result = call_user_func(
            array($this->_class, 'getPreviousAndNextImagePage'), $component, array(), true
        );
        foreach ($result as $r) {
            if (!$r) continue;
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $r->dbId
            ));
        }
    }

    public function onContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->dbId.'_imagePage'
        ));
    }

    public function onClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($event->componentId, array('ignoreVisible'=>true))
            ->getChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component->dbId
            ));
        }
    }
}
