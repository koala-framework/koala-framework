<?php
abstract class Vps_Component_Cache
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

    public static function setInstance($backend)
    {
        self::clearInstance();
        self::$_backend = $backend;
        self::saveStaticMeta();
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public static function saveStaticMeta()
    {
        // getStaticCacheMeta
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
        // für componentLink: alle Komponenten, die als Page erstellt werden können
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $setting) {
                $generator = current(Vps_Component_Generator_Abstract::getInstances(
                    $class, array('generator' => $key))
                );
                if ($generator &&
                    $generator->getGeneratorFlag('page') &&
                    $generator->getGeneratorFlag('table')
                ) {
                    $pattern = '{id}';
                    if (isset($setting['dbIdShortcut'])) {
                        $pattern = $setting['dbIdShortcut'] . $pattern;
                    }
                    $meta = new Vps_Component_Cache_Meta_Static_ComponentLink($generator->getModel(), $pattern);
                    foreach ($generator->getChildComponentClasses() as $c) {
                        self::getInstance()->saveMeta($c, $meta);
                    }
                }
            }
        }
        self::getInstance()->writeBuffer();
    }

    public function saveMeta($componentClass, Vps_Component_Cache_Meta_Abstract $meta)
    {
        $component = null;
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
                $this->_saveMetaModel($componentClass, $modelName, $pattern, get_class($meta), $meta->getParams());
            }

        } else if ($meta instanceof Vps_Component_Cache_Meta_ModelField) {

            $this->_saveMetaRow($component, $meta->getModelname(), $meta->getColumn(), $meta->getValue($component), get_class($meta));

        } else if ($meta instanceof Vps_Component_Cache_Meta_Static_Component) {

            $sourceComponentClass = $meta->getComponentClass();
            if ($sourceComponentClass == $componentClass)
                throw new Vps_Exception('Source and target component must be different, both have ' . $componentClass);
            $this->_saveMetaComponent('', $sourceComponentClass, '', $componentClass, get_class($meta));

        } else if ($meta instanceof Vps_Component_Cache_Meta_Component) {

            $source = $meta->getSourceComponent();
            $target = $component;
            if ($source->dbId == $target->dbId)
                throw new Vps_Exception('Source and target component must be different, both have ' . $component->dbId);
            $this->_saveMetaComponent($source->dbId, $source->componentClass, $target->dbId, $target->componentClass, get_class($meta));

        } else if ($meta instanceof Vps_Component_Cache_Meta_Static_Chained) {

            $sourceComponentClass = $meta->getSourceComponentClass();
            $targetComponentClass = $componentClass;
            if ($sourceComponentClass == $targetComponentClass)
                throw new Vps_Exception('Source and target component Class must be different, both have ' . $sourceComponentClass);
            $this->_saveMetaChained($sourceComponentClass, $targetComponentClass);

        } else {

            throw new Vps_Exception('Unknow Meta: ' . get_class($meta));

        }
    }

    protected abstract function _saveMetaModel($componentClass, $modelName, $pattern, $metaClass, $pattern);
    protected abstract function _saveMetaRow(Vps_Component_Data $component, $modelName, $column, $value, $metaClass);
    protected abstract function _saveMetaComponent($componentId, $componentClass, $targetComponentId, $targetComponentClass, $metaClass);

    protected function _getModelname($row)
    {
        if ($row instanceof Vps_Model_Row_Abstract) {
            $model = $row->getModel();
            if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
        } else if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $model = $row->getTable();
        } else {
            throw new Vps_Exception('row must be instance of Vps_Model_Row_Abstract or Zend_Db_Table_Row_Abstract');
        }
        return get_class($model);
    }

    protected function _getComponentIdsFromWheres($wheres)
    {
        $ret = array();
        foreach ($wheres as $where) {
            foreach ($where as $w) {
                if (isset($w['db_id'])) {
                    if (is_array($w['db_id'])) {
                        $ret = array_merge($ret, $w['db_id']);
                    } else {
                        $ret[] = $w['db_id'];
                    }
                }
            }
        }
        return array_unique($ret);
    }

    public function cleanByRow($row, $dirtyColumns = array())
    {
        //p($this->_getModelname($row));
        $wheres = array();
        $wheres = $this->_addRowWhere($wheres, $row);
        $wheres = $this->_addModelWhere($wheres, $row, $dirtyColumns);
        $wheres = $this->_addComponentWhere($wheres);
        $wheres = $this->_addChainedWhere($wheres);
        $this->_cleanByWheres($wheres);

        // Callback
        $wheres = array();
        $wheres = $this->_addRowWhere($wheres, $row, Vps_Component_Cache_Meta_Abstract::META_TYPE_CALLBACK);
        $wheres = $this->_addModelWhere($wheres, $row, $dirtyColumns, Vps_Component_Cache_Meta_Abstract::META_TYPE_CALLBACK);
        $wheres = $this->_addComponentWhere($wheres);
        $wheres = $this->_addChainedWhere($wheres);
        foreach ($this->_getComponentIdsFromWheres($wheres) as $componentId) {
            $component = Vps_Component_Data_Root::getInstance()->getComponentById(
                $componentId, array('ignoreVisible' => true)
            );
            if ($component) $component->getComponent()->onCacheCallback($row);
        }
    }

    public function writeBuffer()
    {
    }
}
