<?php
class Vps_Component_Cache
{
    static private $_instance;
    static private $_backend = self::CACHE_BACKEND_MYSQL;
    const CACHE_BACKEND_MYSQL = 'Vps_Component_Cache_Mysql';
    const CACHE_BACKEND_MONGO = 'Vps_Component_Cache_Mongo';
    const CACHE_BACKEND_FNF = 'Vps_Component_Cache_Fnf';

    /**
     * @return Vps_Component_Cache_Mysql
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            $backend = self::$_backend;
            self::$_instance = new $backend();
        }
        return self::$_instance;
    }

    public static function setBackend($backend)
    {
        self::clearInstance();
        self::$_backend = $backend;
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public static function saveStaticMeta()
    {
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            $class = $componentClass;
            if (($pos = strpos($class, '.')) !== false)
                $class = substr($componentClass, 0, $pos);
            if (!is_instance_of($class, 'Vpc_Abstract')) continue;
            $metas = call_user_func(array($class, 'getStaticCacheMeta'), $componentClass);
            foreach ($metas as $meta) {
                self::getInstance()->saveMeta($componentClass, $meta);
            }
        }
    }

    public function saveMeta($componentClass, Vps_Component_Cache_Meta_Abstract $meta)
    {
        if ($componentClass instanceof Vps_Component_Data) {
            $component = $componentClass;
            $componentClass = $component->componentClass;
        }
        if ($meta instanceof Vps_Component_Cache_Meta_Static_GeneratorRow) {
            foreach ($meta->getCacheMeta($componentClass) as $meta) {
                $this->saveMeta($componentClass, $meta);
            }
        } else if ($meta instanceof Vps_Component_Cache_Meta_Static_Abstract) {
            $modelName = $meta->getModelname($componentClass);
            if ($modelName) {
                $pattern = $meta->getPattern();
                /*
                echo substr(strrchr(get_class($meta), '_'), 1) . ': ' .
                    $componentClass . ': ' .
                    $modelName . ' (' .
                    $pattern . ")\n";
                    */
            }
        } else if ($meta instanceof Vps_Component_Cache_Meta_ModelField) {
        } else if ($meta instanceof Vps_Component_Cache_Meta_Component) {

        } else {
            throw new Vps_Exception('Unknow Meta: ' . get_class($meta));
        }
    }
}
