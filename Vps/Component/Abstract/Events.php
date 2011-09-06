<?php
class Vps_Component_Abstract_Events
{
    protected $_class;

    const EVENT_ROW_UPDATE = 'rowUpdate';
    const EVENT_COMPONENT_CONTENT_CHANGE = 'componentContentChange';
    const EVENT_COMPONENT_HAS_CONTENT_CHANGE = 'componentHasContentChange';


    protected function __construct($class)
    {
        $this->_class = $class;
        $this->_init();
    }

    protected function _init()
    {
    }

    /**
     * @return $this
     */
    public static function getInstance($componentClass)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Vpc_Admin::getComponentClass($componentClass, 'Events');
            if (!$c) { return null; }
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }

    public static function getAllListeners()
    {
        static $listeners; // todo: cache in apc
        if (!isset($listeners)) {
            $listeners = array();
            $eventObjects = array();
            foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
                $eventObjects[] = Vps_Component_Abstract_Events::getInstance($componentClass);
            }
            $eventObjects[] = Vps_Component_Cache::getInstance();
            foreach ($eventObjects as $eventObject)
                foreach ($eventObject->getListeners() as $listener) {
                    // todo: make it failproof
                    $class = $listener['class'];
                    $event = $listener['event'];
                    $callbackClass = get_class($eventObject);
                    $callbackMethod = $listener['callback'];
                    if (!isset($listeners[$event][$class][$callbackClass])) {
                        $listeners[$event][$class][$callbackClass] = array();
                    }
                    $listeners[$event][$class][$callbackClass][] = $callbackMethod;
                }
        }
        d($listeners);
        return $listeners;
    }

    public static function fireEvent($event, $class, $data)
    {
        foreach (self::getAllListeners() as $listener) {
            if ($listener['event'] == $event && $listener['class'] == $class) {
                $ev->{$listener['callback']}($event, $class, $data);
            }
        }
    }

    protected function _getComponentsByDbIdOwnClass($dbId)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentsByDbId($dbId, array('componentClass'=>$this->_class));
    }
}
