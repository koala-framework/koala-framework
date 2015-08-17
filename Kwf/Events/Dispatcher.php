<?php
class Kwf_Events_Dispatcher
{
    public static $_indent = 0;
    private static $_listeners;
    private static $_subscribedModels;
    public static $eventsCount = 0;

    public static final function getAllListeners()
    {
        if (!isset(self::$_listeners)) {
            self::$_subscribedModels = array();
            if (!Kwf_Component_Settings::$_rootComponentClassSet && file_exists('build/events/listeners')) {
                self::$_listeners = unserialize(file_get_contents('build/events/listeners'));
            } else {
                self::$_listeners = self::_getAllListeners();
            }
        }

        return self::$_listeners;
    }

    public static function clearCache()
    {
        self::$_listeners = null;
        self::$_subscribedModels = null;
    }

    /**
     * Add an additional event subscriber, can be used by unit tests
     *
     * obj
     */
    public static function addListeners($objOrClass)
    {
        self::getAllListeners(); //fill cache
        if ($objOrClass instanceof Kwf_Events_Subscriber) {
            self::_addListenersFromSubscribers(self::$_listeners, array($objOrClass));
        } else if (is_string($objOrClass)) {
            if (is_instance_of($objOrClass, 'Kwf_Model_Interface')) {
                self::_addListenersFromSubscribers(self::$_listeners,
                    self::_getSubscribersFromModel($objOrClass)
                );
            } else if (is_instance_of($objOrClass, 'Kwf_Component_Abstract')) {
                self::_addListenersFromSubscribers(self::$_listeners,
                    self::_getSubscribersFromComponent($objOrClass)
                );
            }
        }
    }

    private static function _getSubscribersFromComponent($componentClass)
    {
        $subscribers = array();

        $eventsClass = Kwc_Admin::getComponentClass($componentClass, 'Events');
        $subscribers[] = Kwf_Component_Abstract_Events::getInstance(
                $eventsClass, array('componentClass' => $componentClass)
        );

        foreach (Kwc_Abstract::getSetting($componentClass, 'generators') as $generatorKey => $null) {
            $generator = current(Kwf_Component_Generator_Abstract::getInstances(
                    $componentClass, array('generator' => $generatorKey))
            );
            $eventsClass = $generator->getEventsClass();
            if ($eventsClass) {
                $subscribers[] = Kwf_Component_Generator_Events::getInstance(
                        $eventsClass,
                        array(
                                'componentClass' => $componentClass,
                                'generatorKey' => $generatorKey
                        )
                );
            }
        }

        if (Kwc_Abstract::hasSetting($componentClass, 'menuConfig')) {
            $mc = Kwf_Component_Abstract_MenuConfig_Abstract::getInstance($componentClass);
            $eventsClass = $mc->getEventsClass();
            if ($eventsClass) {
                $subscribers[] = Kwf_Component_Abstract_MenuConfig_Events::getInstance(
                        $eventsClass,
                        array(
                            'componentClass' => $componentClass
                        )
                );
            }
        }

        return $subscribers;
    }

    private static function _getSubscribersFromModel($model)
    {
        $model = Kwf_Model_Abstract::getInstance($model);
        $id = $model->getFactoryId();
        if (!$id) {
            //can't subscribe
            return array();
        }
        if (isset(self::$_subscribedModels[$id])) {
            //already subscribed
            return array();
        }
        self::$_subscribedModels[$id] = true;

        return $model->getEventSubscribers();
    }

    private static function _getAllListeners()
    {
        Kwf_Events_ModelObserver::getInstance()->disable();
        $models = array();
        $subscribers = array();
        $hasFulltext = false;

        foreach (Kwc_Abstract::getComponentClasses() as $componentClass) {
            $subscribers = array_merge($subscribers, self::_getSubscribersFromComponent($componentClass));
            if (Kwc_Abstract::getFlag($componentClass, 'usesFulltext')) {
                $hasFulltext = true;
            }
        }

        if (Kwf_Component_Data_Root::getComponentClass()) {
            $subscribers[] = Kwf_Events_Subscriber::getInstance('Kwf_Component_Events_ViewCache');
            $subscribers[] = Kwf_Events_Subscriber::getInstance('Kwf_Component_Events_UrlCache');
            $subscribers[] = Kwf_Events_Subscriber::getInstance('Kwf_Component_Events_ProcessInputCache');
        }
        if ($hasFulltext) {
            $subscribers[] = Kwf_Events_Subscriber::getInstance('Kwf_Component_Events_Fulltext');
        }
        foreach (Kwf_Model_Abstract::findAllInstances() as $m) {
            $subscribers = array_merge($subscribers, self::_getSubscribersFromModel($m));
        }

        $ret = array();
        self::_addListenersFromSubscribers($ret, $subscribers);
        Kwf_Events_ModelObserver::getInstance()->enable();
        return $ret;
    }

    static private function _addListenersFromSubscribers(array &$listeners, $subscribers)
    {
        $uniqueSubscribers = array();
        foreach ($subscribers as $subscriber) {
            $key = get_class($subscriber).serialize($subscriber->getConfig());
            if (!isset($uniqueSubscribers[$key])) {
                $uniqueSubscribers[$key] = $subscriber;
            }
        }
        foreach ($uniqueSubscribers as $subscriber) {
            foreach ($subscriber->getListeners() as $listener) {
                if (!is_array($listener) ||
                        !isset($listener['event']) ||
                        !isset($listener['callback'])
                ) {
                    throw new Kwf_Exception('Listeners of ' . get_class($subscriber) . ' must return arrays with keys "class" (optional), "event" and "callback"');
                }
                $event = $listener['event'];
                if (!class_exists($event)) throw new Kwf_Exception("Event-Class $event not found, comes from " . get_class($subscriber));
                $class = isset($listener['class']) ? $listener['class'] : 'all';
                if (!is_array($class)) {
                    $class = array($class);
                }
                foreach ($class as $c) {
                    if (is_object($c)) {
                        if ($c instanceof Kwf_Model_Abstract) {
                            $c = $c->getFactoryId();
                        } else {
                            $c = get_class($c);
                        }
                    }
                    $listeners[$event][$c][] = array(
                            'class' => get_class($subscriber),
                            'method' => $listener['callback'],
                            'config' => $subscriber->getConfig()
                    );
                }
            }
        }
        return $listeners;
    }

    public static function fireEvent($event)
    {
        $logger = Kwf_Events_Log::getInstance();
        if ($logger && $logger->indent == 0) {
            $logger->info('----');
            $logger->resetTimer();
        }


        $class = $event->class;
        $eventClass = get_class($event);

        $cacheId = '-ev-lst-'.Kwf_Component_Data_Root::getComponentClass().'-'.$eventClass.'-'.$class;
        $callbacks = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if ($callbacks === false) {
            $listeners = self::getAllListeners();
            $callbacks = array();
            if ($class && isset($listeners[$eventClass][$class])) {
                $callbacks = $listeners[$eventClass][$class];
            }
            if (isset($listeners[$eventClass]['all'])) {
                $callbacks = array_merge($callbacks, $listeners[$eventClass]['all']);
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $callbacks);
        }

        if ($logger) {
            $logger->info($event->__toString() . ':');
            $logger->indent++;
        }
        static $callbackBenchmark = array();
        foreach ($callbacks as $callback) {
            $ev = call_user_func(
                array($callback['class'], 'getInstance'),
                $callback['class'],
                $callback['config']
            );
            if ($logger) {
                $msg = '-> '.$callback['class'] . '::' . $callback['method'] . '(' . Kwf_Debug::_btArgsString($callback['config']) . ')';
                $logger->info($msg . ':');
                $start = microtime(true);
            }
            $ev->{$callback['method']}($event);
            if ($logger) {
                if (!isset($callbackBenchmark[$callback['class'] . '::' . $callback['method']])) {
                    $callbackBenchmark[$callback['class'] . '::' . $callback['method']] = array(
                        'calls' => 0,
                        'time' => 0
                    );
                }
                $callbackBenchmark[$callback['class'] . '::' . $callback['method']]['calls']++;
                $callbackBenchmark[$callback['class'] . '::' . $callback['method']]['time'] += (microtime(true)-$start)*1000; //ATM includes everything which is missleading
            }
        }
        if ($logger) {
            $logger->indent--;
            if ($logger->indent == 0) {
                foreach ($callbackBenchmark as $cb=>$i) {
                    $logger->info(sprintf("% 3d", $i['calls'])."x ".sprintf("%3d", round($i['time'], 0))." ms: $cb");
                }
                $callbackBenchmark = array();
            }
        }

        self::$eventsCount++;
    }
}
