<?php
class Vps_Component_Events
{
    const EVENT_ROW_DELETE = 'rowDelete';
    const EVENT_ROW_UPDATE = 'rowUpdate';
    const EVENT_ROW_INSERT = 'rowInsert';
    const EVENT_ROW_UPDATES_FINISHED = 'rowUpdatesFinished';

    const EVENT_MODEL_UPDATE = 'modelUpdate';

    const EVENT_COMPONENT_CONTENT_CHANGE = 'componentContentChange';
    const EVENT_COMPONENT_HAS_CONTENT_CHANGE = 'componentHasContentChange';

    const EVENT_COMPONENT_REMOVE = 'componentRemove';
    const EVENT_COMPONENT_ADD = 'componentAdd';
    const EVENT_COMPONENT_MOVE = 'componentMove';
    const EVENT_COMPONENT_CLASS_CHANGE = 'componentClassChange';

    const EVENT_PAGE_PARENT_CHANGE = 'pageParentChange';
    const EVENT_PAGE_MOVE = 'pageMove';
    const EVENT_PAGE_CLASS_CHANGE = 'pageClassChange';
    const EVENT_PAGE_REMOVE = 'pageRemove';
    const EVENT_PAGE_ADD = 'pageAdd';

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
        return array(); // class, event, callback
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
                $callback['config']
            );
            $ev->{$callback['method']}($event, $data);
        }
    }
}
