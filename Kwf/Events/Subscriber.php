<?php
class Kwf_Events_Subscriber
{
    private static $_instances = array();

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

    protected function _getClassFromRow($classes, $row, $cleanValue = false)
    {
        if (count($classes) > 1 && $row->getModel()->hasColumn('component')) {
            if ($cleanValue) {
                $c = $row->getCleanValue('component');
            } else {
                $c = $row->component;
            }
            if (isset($classes[$c])) {
                return $classes[$c];
            }
        }
        $class = array_shift($classes);
        return $class;
    }

    protected function _init() {}

    public function getListeners()
    {
        return array();
    }

    public static function fireEvent($event)
    {
        Kwf_Events_Dispatcher::fireEvent($event);
    }

    /**
     * @return $this
     */
    public static final function getInstance($class, $config = array())
    {
        $id = md5(serialize(array($class, $config)));
        if (!isset(self::$_instances[$id])) {
            if (!$class) {
                throw new Kwf_Exception("No class given");
            }
            self::$_instances[$id] = new $class($config);
        }
        return self::$_instances[$id];
    }

    public static final function clearInstances()
    {
        self::$_instances = array();
    }
}
