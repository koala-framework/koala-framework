<?php
class Kwc_Directories_List_Feed_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();

        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (in_array('Kwc_Directories_List_Component', Kwc_Abstract::getParentClasses($class)) || in_array('Kwc_Directories_List_Trl_Component', Kwc_Abstract::getParentClasses($class))) {
                if (Kwc_Abstract::hasChildComponentClass($class, 'feed')
                    && $this->_class == Kwc_Abstract::getChildComponentClass($class, 'feed')
                ) {
                    $directoryClasses = call_user_func(
                        array(strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class, 'getItemDirectoryClasses'), $class
                    );
                    foreach ($directoryClasses as $directoryClass) {
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventItemInserted',
                            'callback' => 'onDirectoryUpdate'
                        );
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventItemDeleted',
                            'callback' => 'onDirectoryUpdate'
                        );
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventItemUpdated',
                            'callback' => 'onDirectoryUpdate'
                        );
                        $ret[] = array(
                            'class' => $directoryClass,
                            'event' => 'Kwc_Directories_List_EventItemsUpdated',
                            'callback' => 'onDirectoryUpdate'
                        );
                    }
                }
            }
        }
        return $ret;
    }

    public function onDirectoryUpdate(Kwc_Directories_List_EventItemAbstract $event)
    {
        $gen = Kwf_Component_Generator_Abstract::getInstance($event->class, 'detail');
        $datas = $gen->getChildData(null, array('id' => $event->itemId));
        if (isset($datas[0])) {
            $feed = $datas[0]->parent->getChildComponent('_feed');
            Kwf_Cache_Simple::delete('feed-' . $feed->componentId);
        }
    }
}
