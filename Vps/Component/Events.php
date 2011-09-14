<?php
class Vps_Component_Events
{
    protected $_config;

    protected function __construct($config = array())
    {
        $this->_config = $config;
        $this->_init();
    }

    public function getConfig()
    {
        return $this->_config;
    }

    protected function _init() {}

    /**
     * @return $this
     */
    public static final function getInstance($class, $config = array())
    {
        $id = md5(serialize(array($class, $config)));
        static $instances = array();
        if (!isset($instances[$id])) {
            $instances[$id] = new $class($config);
        }
        return $instances[$id];
    }

    public static final function getAllListeners()
    {
        $cacheId = 'Vps_Component_Events_listeners';
        $listeners = Vps_Cache_Simple::fetch($cacheId);
        if (!$listeners) {

            $eventObjects = array();
            foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
                $eventsClass = Vpc_Admin::getComponentClass($componentClass, 'Events');
                $eventObjects[] = Vps_Component_Abstract_Events::getInstance(
                    $eventsClass, array('componentClass' => $componentClass)
                );
                foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $generatorKey => $null) {
                    $generator = current(Vps_Component_Generator_Abstract::getInstances(
                        $componentClass, array('generator' => $generatorKey))
                    );
                    $eventsClass = $generator->getEventsClass();
                    if ($eventsClass) {
                        $eventObjects[] = Vps_Component_Generator_Events::getInstance(
                            $eventsClass,
                            array(
                                'componentClass' => $componentClass,
                                'generatorKey' => $generatorKey
                            )
                        );
                    }
                }
            }
            $eventObjects[] = Vps_Component_Events_ViewCache::getInstance('Vps_Component_Events_ViewCache');

            $listeners = array();
            foreach ($eventObjects as $eventObject) {
                foreach ($eventObject->getListeners() as $listener) {
                    if (!is_array($listener) ||
                        !isset($listener['event']) ||
                        !isset($listener['callback'])
                    ) {
                        throw new Vps_Exception('Listeners of ' . get_class($eventObject) . ' must return arrays with keys "class" (optional), "event" and "callback"');
                    }
                    $event = $listener['event'];
                    if (!class_exists($event)) throw new Vps_Exception("Event-Class $event not found, comes from " . get_class($eventObject));
                    $class = isset($listener['class']) ? $listener['class'] : 'all';
                    $listeners[$event][$class][] = array(
                        'class' => get_class($eventObject),
                        'method' => $listener['callback'],
                        'config' => $eventObject->getConfig()
                    );
                }
            }

            Vps_Cache_Simple::add($cacheId, $listeners);
        }
        return $listeners;
    }

    public function getListeners()
    {
        return array();
    }

    public static function fireEvent($event)
    {
        $listeners = self::getAllListeners();
        $class = $event->class;
        $eventClass = get_class($event);
        $callbacks = array();
        if ($class && isset($listeners[$eventClass][$class])) {
            $callbacks = $listeners[$eventClass][$class];
        }
        if (isset($listeners[$eventClass]['all'])) {
            $callbacks = array_merge($callbacks, $listeners[$eventClass]['all']);
        }
        foreach ($callbacks as $callback) {
            $ev = call_user_func(
                array($callback['class'], 'getInstance'),
                $callback['class'],
                $callback['config']
            );
            $ev->{$callback['method']}($event);
        }
    }
}
