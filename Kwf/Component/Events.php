<?php
class Kwf_Component_Events
{
    protected $_config;
    public static $_indent = 0;

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
        $cacheId = 'Kwf_Component_Events_listeners';
        $listeners = Kwf_Cache_Simple::fetch($cacheId);
        if (!$listeners) {

            $eventObjects = array();
            foreach (Kwc_Abstract::getComponentClasses() as $componentClass) {
                $eventsClass = Kwc_Admin::getComponentClass($componentClass, 'Events');
                $eventObjects[] = Kwf_Component_Abstract_Events::getInstance(
                    $eventsClass, array('componentClass' => $componentClass)
                );
                foreach (Kwc_Abstract::getSetting($componentClass, 'generators') as $generatorKey => $null) {
                    $generator = current(Kwf_Component_Generator_Abstract::getInstances(
                        $componentClass, array('generator' => $generatorKey))
                    );
                    $eventsClass = $generator->getEventsClass();
                    if ($eventsClass) {
                        $eventObjects[] = Kwf_Component_Generator_Events::getInstance(
                            $eventsClass,
                            array(
                                'componentClass' => $componentClass,
                                'generatorKey' => $generatorKey
                            )
                        );
                    }
                }
            }
            $eventObjects[] = self::getInstance('Kwf_Component_Events_ViewCache');
            $eventObjects[] = self::getInstance('Kwf_Component_Events_UrlCache');

            $listeners = array();
            foreach ($eventObjects as $eventObject) {
                if (get_class($eventObject) == 'Kwf_Component_Generator_Events_Table') {

                }

                foreach ($eventObject->getListeners() as $listener) {
                    if (!is_array($listener) ||
                        !isset($listener['event']) ||
                        !isset($listener['callback'])
                    ) {
                        throw new Kwf_Exception('Listeners of ' . get_class($eventObject) . ' must return arrays with keys "class" (optional), "event" and "callback"');
                    }
                    $event = $listener['event'];
                    if (!class_exists($event)) throw new Kwf_Exception("Event-Class $event not found, comes from " . get_class($eventObject));
                    $class = isset($listener['class']) ? $listener['class'] : 'all';
                    if (is_object($class)) $class = get_class($class);
                    $listeners[$event][$class][] = array(
                        'class' => get_class($eventObject),
                        'method' => $listener['callback'],
                        'config' => $eventObject->getConfig()
                    );
                }
            }

            Kwf_Cache_Simple::add($cacheId, $listeners);
        }
        return $listeners;
    }

    public function getListeners()
    {
        return array();
    }

    public static function fireEvent($event)
    {
        if ($event instanceof Kwf_Component_Event_Row_Abstract ||
            $event instanceof Kwf_Component_Event_Row_UpdatesFinished)
        {
            self::$_indent = 0;
        }

        $logger = null;
        if (Zend_Registry::get('config')->debug->eventlog) {
            $logger = Kwf_Component_Events_Log::getInstance();
            if (self::$_indent == 0) $logger->info('----');
        }

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
        if (!empty($callbacks)) self::$_indent++;
        $loopIndent = self::$_indent;
        foreach ($callbacks as $callback) {
            $ev = call_user_func(
                array($callback['class'], 'getInstance'),
                $callback['class'],
                $callback['config']
            );
            if ($logger) {
                $logger->logEvent($loopIndent, $callback, $event);
            }
            self::$_indent = $loopIndent;
            $ev->{$callback['method']}($event);
        }
    }
}
