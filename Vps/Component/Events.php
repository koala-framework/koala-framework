<?php
class Vps_Component_Events
{
    const EVENT_ROW_UPDATE = 'rowUpdate';
    const EVENT_ROW_UPDATES_FINISHED = 'rowUpdatesFinished';
    const EVENT_COMPONENT_CONTENT_CHANGE = 'componentContentChange';
    const EVENT_COMPONENT_HAS_CONTENT_CHANGE = 'componentHasContentChange';

    protected $_class;

    protected function __construct($class)
    {
        $this->_class = $class;
    }

    public function getClass()
    {
        return $this->_class;
    }

    /**
     * @return $this
     */
    public static final function getInstance($class, $contextClass = null)
    {
        static $instances = array();
        if (!$contextClass) $contextClass = $class;
        if (!isset($instances[$contextClass])) {
            $class = call_user_func(array($class, 'getGetInstanceClass'), $contextClass);
            if (!$class) { return null; }
            $instances[$contextClass] = new $class($contextClass);
        }
        return $instances[$contextClass];
    }

    public static function getGetInstanceClass($componentClass)
    {
        return $componentClass;
    }

    public static final function getAllListeners()
    {
        static $cache = null;
        if (!$cache) {
            $cache = Vps_Cache::factory('Core', 'Apc', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
        }
        $cacheId = 'Vps_Component_Events_listeners';

        $listeners = $cache->load($cacheId);
        if (!$listeners) {
            $listeners = array();
            $eventObjects = array();
            foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
                $eventObjects[] = Vps_Component_Abstract_Events::getInstance(
                    'Vps_Component_Abstract_Events', $componentClass
                );
            }
            $eventObjects[] = Vps_Component_Events_ViewCache::getInstance('Vps_Component_Events_ViewCache');
            foreach ($eventObjects as $eventObject) {
                foreach ($eventObject->getListeners() as $listener) {
                    // todo: make it failproof
                    $event = $listener['event'];
                    $class = isset($listener['class']) ? $listener['class'] : 'all';
                    $listeners[$event][$class][] = array(
                        'class' => get_class($eventObject),
                        'method' => $listener['callback'],
                        'getInstanceClass' => $eventObject instanceof Vps_Component_Abstract_Events ?
                            $eventObject->getClass() :
                            null
                    );
                }
            }
            $cache->save($listeners, $cacheId);
        }
        return $listeners;
    }

    public function getListeners()
    {
        return array();
    }

    public static function fireEvent($event, $class = null, $data = null)
    {
        $listeners = self::getAllListeners();
        $callbacks = array();
        if ($class && isset($listeners[$event][$class])) {
            $callbacks = $listeners[$event][$class];
        }
        if (isset($listeners[$event]['all'])) {
            $callbacks = array_merge($callbacks, $listeners[$event]['all']);
        }
        foreach ($callbacks as $callback) {
            $ev = call_user_func(
                array($callback['class'], 'getInstance'),
                $callback['class'],
                $callback['getInstanceClass']
            );
            $ev->{$callback['method']}($event, $data);
        }
    }
}
