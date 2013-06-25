<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        //find components that can create ourself ($this->_class)
        foreach ($this->_getCreatingClasses($this->_class) as $class) {
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
                    $imageEnlargeClasses = array_merge($imageEnlargeClasses, $this->_getCreatingClasses($c));
                } else {
                    $imageEnlargeClasses[] = $c;
                }
            }

            foreach ($imageEnlargeClasses as $imageEnlargeClass) {
                // TODO: does not cover "List_Switch with ImageEnlarge as large component (we have to go up one more level)"
                foreach ($this->_getCreatingClasses($imageEnlargeClass, null) as $listClass) {
                    if (!is_instance_of($listClass, 'Kwc_Abstract_List_Component')) {
                        $listClasses = $this->_getCreatingClasses($listClass, 'Kwc_Abstract_List_Component');
                    } else {
                        $listClasses = array($listClass);
                    }
                    foreach ($listClasses as $listClass) {
                        $ret[] = array(
                            'class' => $listClass,
                            'event' => 'Kwc_Abstract_List_EventItemDeleted',
                            'callback' => 'onListItemChange'
                        );
                        $ret[] = array(
                            'class' => $listClass,
                            'event' => 'Kwc_Abstract_List_EventItemInserted',
                            'callback' => 'onListItemChange'
                        );
                        $ret[] = array(
                            'class' => $listClass,
                            'event' => 'Kwc_Abstract_List_EventItemUpdated',
                            'callback' => 'onListItemChange'
                        );
                    }
                }
            }
        }
        return $ret;
    }

    public function onListItemChange(Kwf_Component_Event_Component_Abstract $event)
    {
        // get previous and next items and delete the cache for them
        $c = $event->component;
        $getChildren = array();
        if ($c->componentClass != $this->_class) {
            $c = $c->getRecursiveChildComponents(array('componentClass'=>$this->_class));
            if (count($c) != 1) throw new Kwf_Exception("only a single component should exist");
            $c = $c[0];
            $i = $c->parent->parent;
            if (is_instance_of($i->componentClass, 'Kwc_Basic_LinkTag_Component')) {
                $i = $i->parent;
            }
            while ($i != $event->component) {
                $getChildren[] = $i->generator->getIdSeparator().$i->id;
                $i = $i->parent;
            }
        }
        $result = call_user_func(
            array($this->_class, 'getPreviousAndNextImagePage'), $this->_class, $event->component, $getChildren, true
        );

        foreach ($result as $r) {
            if (!$r) continue;
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $r
            ));
        }
    }

    public function onContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $page = $event->component->getChildComponent('_imagePage');
        if ($page) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $page
            ));
        }
    }

    public function onClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    public function onMediaChanged(Kwf_Component_Event_Media_Changed $event)
    {
        $components = $event->component
            ->getChildComponents(array('componentClass' => $this->_class));
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }
}
