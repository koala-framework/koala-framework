<?php
abstract class Kwf_Component_Cache
{
    static private $_instance;
    static private $_backend = self::CACHE_BACKEND_MYSQL;
    const CACHE_BACKEND_MYSQL = 'Kwf_Component_Cache_Mysql';
    const CACHE_BACKEND_FNF = 'Kwf_Component_Cache_Fnf';
    const NO_CACHE = '{nocache}';

    /**
     * @return Kwf_Component_Cache_Mysql
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            $backend = self::$_backend;
            self::$_instance = new $backend();
        }
        return self::$_instance;
    }

    public static function setInstance($backend)
    {
        self::clearInstance();
        self::$_backend = $backend;
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public abstract function deleteViewCache($select);

    public function writeBuffer()
    {
        foreach ($this->_models as $m) {
            if (is_object($m)) $m->writeBuffer();
        }
    }
/*
    public static function saveStaticMeta()
    {
        // getStaticCacheMeta
        foreach (Kwc_Abstract::getComponentClasses() as $componentClass) {
            $class = $componentClass;
            if (($pos = strpos($class, '.')) !== false)
                $class = substr($componentClass, 0, $pos);
            if (!is_instance_of($class, 'Kwc_Abstract')) continue;
            $metas = call_user_func(array($class, 'getStaticCacheMeta'), $componentClass);
            foreach ($metas as $meta) {
                self::getInstance()->saveMeta($componentClass, $meta);
            }
        }
        // für componentLink+UrlCache: alle Komponenten, die als Page erstellt werden können
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $key => $setting) {
                if (!isset($setting['class'])) continue;
                $generator = current(Kwf_Component_Generator_Abstract::getInstances(
                    $class, array('generator' => $key))
                );
                if ($generator &&
                    $generator->getGeneratorFlag('page') &&
                    $generator->getGeneratorFlag('table')
                ) {
                    //ComponentLink
                    $model = $generator->getModel();
                    if ($model instanceof Kwc_Root_Category_GeneratorModel) {
                        $primaryKey = 'id';
                    } else {
                        $primaryKey = $model->getPrimaryKey();
                    }
                    $pattern = '{' . $primaryKey . '}';
                    if (isset($setting['dbIdShortcut'])) {
                        $pattern = $setting['dbIdShortcut'] . $pattern;
                    }
                    $meta = new Kwf_Component_Cache_Meta_Static_ComponentLink($model, $pattern);
                    foreach ($generator->getChildComponentClasses() as $c) {
                        self::getInstance()->saveMeta($c, $meta);
                    }
                    // Komponenten, die im Seitenbaum vorkommen
                    if ($generator->getModel() instanceof Kwc_Root_Category_GeneratorModel) {
                        $meta = new Kwf_Component_Cache_Meta_Static_ComponentLink('Kwf_Component_Model');
                        foreach ($generator->getChildComponentClasses() as $c) {
                            self::getInstance()->saveMeta($c, $meta);
                        }
                        // Wenns ein hascontent(menu) im master.tpl gibt
                        $meta = new Kwc_Menu_Abstract_CacheMetaMaster('Kwf_Component_Model');
                        foreach ($generator->getChildComponentClasses() as $c) {
                            self::getInstance()->saveMeta($c, $meta);
                        }
                    }
                }
                if ($generator &&
                    $generator->getGeneratorFlag('table')
                ) {
                    //UrlCache
                    $meta = new Kwf_Component_Cache_Meta_Static_UrlCache($generator);
                    self::getInstance()->saveMeta($class, $meta); //$class wird nicht verwendet, ist aber im primaryKey wird nur darum gesetzt

                    //ProcessInputCache
                    $meta = new Kwf_Component_Cache_Meta_Static_ProcessInputCache($generator);
                    self::getInstance()->saveMeta($class, $meta); //$class wird nicht verwendet, ist aber im primaryKey wird nur darum gesetzt
                }
            }
        }
        self::getInstance()->writeBuffer();
    }
*/
}
