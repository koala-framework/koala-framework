<?php
class Vps_Component_Cache_Mysql extends Vps_Component_Cache
{
    protected $_models;
    const CLEAN_DEFAULT = 'default';
    private $_chainedTypes;

    public function __construct()
    {
        $this->_models = array (
            'cache' => new Vps_Component_Cache_Mysql_Model(),
            'preload' => new Vps_Component_Cache_Mysql_PreloadModel(),
            'metaModel' => new Vps_Component_Cache_Mysql_MetaModelModel(),
            'metaRow' => new Vps_Component_Cache_Mysql_MetaRowModel(),
            'metaComponent' => new Vps_Component_Cache_Mysql_MetaComponentModel(),
            'metaChained' => new Vps_Component_Cache_Mysql_MetaChainedModel()
        );
    }

    /**
     * @return Vps_Model_Abstract
     */
    public function getModel($type = 'cache')
    {
        return isset($this->_models[$type]) ? $this->_models[$type] : null;
    }

    public function save(Vps_Component_Data $component, $content, $type = 'component', $value = '')
    {
        $settings = $component->getComponent()->getViewCacheSettings();

        $page = $component;
        while ($page && !$page->isPage) $page = $page->parent;
        $expire = is_null($settings['lifetime']) ? 0 : time() + $settings['lifetime'];

        $data = array(
            'component_id' => $component->componentId,
            'page_id' => $page ? $page->componentId : null,
            'component_class' => $component->componentClass,
            'type' => $type,
            'value' => $value,
            'expire' => $expire,
            'deleted' => false,
            'content' => $content
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['cache']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
        return true;
    }

    public function load(Vps_Component_Data $component, $type = 'component', $value = '')
    {
        $select = $this->_models['cache']->select()
            ->whereEquals('component_id', $component->componentId)
            ->whereEquals('type', $type)
            ->whereEquals('deleted', false)
            ->whereEquals('value', $value);
        $row = $this->_models['cache']->export(Vps_Model_Db::FORMAT_ARRAY, $select);
        if ($row) return $row[0]['content'];
        return null;
    }

    public function preload(Vps_Component_Data $component)
    {
        $ret = array();

        $select = $this->getModel()->select();
        $select->whereEquals('deleted', false);
        $preloadSelect = $this->getModel('preload')->select();
        $or = array();
        while ($component && !$component->isPage) $component = $component->parent;
        if ($component) {
            $or[] = new Vps_Model_Select_Expr_Equal('page_id', $component->componentId);
            $preloadSelect->whereEquals('page_id', $component->componentId);
        } else {
            $or[] = new Vps_Model_Select_Expr_IsNull('page_id');
            $preloadSelect->whereNull('page_id');
        }
        $preloadIds = array();
        foreach ($this->getModel('preload')->export(Vps_Model_Db::FORMAT_ARRAY, $preloadSelect) as $preload) {
            $preloadIds[] = $preload['preload_id'];
        }
        if ($preloadIds) {
            $or[] = new Vps_Model_Select_Expr_Equal('component_id', $preloadIds);
        }

        while ($component) {
            $component = $component->parent;
            if ($component && $component->isPage) {
                $or[] = new Vps_Model_Select_Expr_Equal('page_id', $component->componentId);
            }
        }
        $or[] = new Vps_Model_Select_Expr_IsNull('page_id');
        $select->where(new Vps_Model_Select_Expr_Or($or));

        foreach ($this->getModel()->export(Vps_Model_Db::FORMAT_ARRAY, $select) as $row) {
            if (!$row['expire'] || $row['expire'] > time()) {
                $ret[$row['type']][(string)$row['component_id']][(string)$row['value']] = $row['content'];
            }
        }
        return $ret;
    }

    public function savePreload(Vps_Component_Data $source, Vps_Component_Data $target)
    {
        if ($source->componentId == $target->componentId)
            throw new Vps_Exception('Source and target component must be different, both have ' . $source->componentId);
        $data = array(
            'page_id' => $source->getPage()->componentId,
            'preload_id' => $target->componentId
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['preload']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

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

    protected function _addRowWhere($wheres, $row, $metaType = Vps_Component_Cache_Meta_Abstract::META_TYPE_DEFAULT)
    {
        // Das suchen wir
        // $searchModel = model, column, value, component_id, meta_class
        // $searchModel->model = $row->model && $searchModel->value=$row->{$searchModel->column}
        $searchModel = $this->getModel('metaRow');

        $select = $searchModel->select()
            ->whereEquals('model', $this->_getModelname($row));

        // Alle Rows mit betreffenden model holen und columns suchen
        $columns = array();
        foreach ($searchModel->getRows($select) as $r) $columns[] = $r->column;
        $columns = array_unique($columns);

        // Für die Columns Values einsetzen
        $or = array();
        foreach ($columns as $column) {
            $or[] = new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Equal('column', $column),
                new Vps_Model_Select_Expr_Equal('value', $row->$column)
            ));
        }

        // Rausgesuchte Columns mit Values zu Select hinzufügen
        if ($or) {
            $select->where(new Vps_Model_Select_Expr_Or($or));
            foreach ($searchModel->getRows($select) as $metaRow) {
                $type = call_user_func(array($metaRow->meta_class, 'getMetaType'));
                if ($type != $metaType) continue;
                $where = call_user_func(
                    array($metaRow->meta_class, 'getDeleteWhere'),
                    $metaRow->component_id
                );
                $wheres[$metaRow->component_class][] = $where;
            }
        }
        return $wheres;
    }

    protected function _addModelWhere($wheres, $row, $metaType = Vps_Component_Cache_Meta_Abstract::META_TYPE_DEFAULT)
    {
        $model = $this->getModel('metaModel');

        $select = $model->select()
            ->whereEquals('model', $this->_getModelname($row));

        foreach ($model->getRows($select) as $metaRow) {
            $type = call_user_func(array($metaRow->meta_class, 'getMetaType'));
            if ($type != $metaType) continue;
            $where = call_user_func(
                array($metaRow->meta_class, 'getDeleteWhere'),
                $metaRow->pattern, $row
            );
            $wheres[$metaRow->component_class][] = $where;
        }
        return $wheres;
    }

    protected function _addComponentWhere($wheres)
    {
        // $model: component_id component_class target_component_id target_component_class
        // So lange $model nach component_id durchsuchen und in ergebnis
        // $model->target_component_id reingeben bis nichts mehr neues kommt
        $ids = $this->_getComponentIdsFromWheres($wheres);

        $model = $this->getModel('metaComponent');
        $ret = array();
        do {
            $select = $model->select();
            $select->whereEquals('component_id', $ids);
            $ids2 = array();
            foreach ($model->getRows($select) as $r) {
                $ids2[] = $r->target_component_id;
                $ret[$r->target_component_class][] = $r->target_component_id;
            }
            $ids2 = array_diff($ids2, $ids);
            $ids = array_unique(array_merge($ids, $ids2));
        } while ($ids2);
        foreach ($ret as $componentClass => $componentIds) {
            $wheres[$componentClass][] = array(
                'componentId' => array_values(array_unique($componentIds))
            );
        }
        return $wheres;
    }

    protected function _addChainedWhere($wheres)
    {
        // chainedTypes im Format chainedTypes['Trl'] = 'Vpc_Chained_Trl_...' holen
        if (is_null($this->_chainedTypes)) {
            foreach (Vps_Component_Abstract::getComponentClasses() as $cc) {
                if (!Vpc_Abstract::hasSetting($cc, 'masterComponentClass')) continue;
                $chainedType = Vpc_Abstract::getFlag($cc, 'chainedType');
                if ($chainedType) $this->_chainedTypes[$chainedType] = $cc;
            }
        }
        $chainedTypes = $this->_chainedTypes;

        $model = $this->getModel('metaChained');
        $select = $model->select()
            ->whereEquals('source_component_class', array_keys($wheres));
        foreach ($model->getRows($select) as $row) { // Alle infrage kommenden target_component_classes

            // Alle master-componentIds der target_component_class
            if (!isset($wheres[$row->source_component_class])) continue;
            $componentIds = $this->_getComponentIdsFromWheres(array($wheres[$row->source_component_class]));
            foreach ($componentIds as $componentId) {

                if (strpos($componentId, '%') !== false) continue;

                // Komponente von Master bei der der Cache gelöscht wird
                $component = Vps_Component_Data_Root::getInstance()
                    ->getComponentById($componentId, array('ignoreVisible' => true));
                if (!$component) continue;

                // Alle zur Mastercomponent gehörigen ChainedComponents finden:
                // Nach oben schauen, wenn chainedType gefunden, statisch die
                // dazugehörigen chained-Class holen und anhand dieser die
                // ChainedComponents finden. Danach einfach die componentId
                // vom Master mit der der Chained ersetzen
                $chainedFound = false;
                $c = $component;
                while ($c) {

                    $chainedType = Vpc_Abstract::getFlag($c->componentClass, 'chainedType');
                    if ($chainedType && isset($chainedTypes[$chainedType])) {
                        $chainedFound = true;
                        $chainedComponents = $c->parent->getChildComponents(array(
                            'componentClass' => $chainedTypes[$chainedType],
                            'ignoreVisible' => true
                        ));
                        foreach ($chainedComponents as $chainedComponent) {
                            $part2 = substr($componentId, strlen($c->componentId));
                            $componentId = $chainedComponent->componentId . $part2;
                            $wheres[$row->target_component_class][] = array(
                                'componentId' => $componentId
                            );
                        }
                    }

                    $c = $c->parent;
                }
                if (!$chainedFound) throw new Vps_Exception("No Flag chainedType set for {$component->componentClass} or parent");

            }

        }
        return $wheres;
    }

    private function _getComponentIdsFromWheres($wheres)
    {
        $ret = array();
        foreach ($wheres as $where) {
            foreach ($where as $w) {
                if (isset($w['componentId'])) {
                    $c = $w['componentId'];
                    $ret[] = $w['componentId'];
                }
            }
        }
        return array_unique($ret);
    }

    public function cleanByRow(Vps_Model_Row_Abstract $row)
    {
        //p($this->_getModelname($row));
        $wheres = array();
        $wheres = $this->_addRowWhere($wheres, $row);
        $wheres = $this->_addModelWhere($wheres, $row);
        $wheres = $this->_addComponentWhere($wheres);
        $wheres = $this->_addChainedWhere($wheres);
        $select = $this->getModel('cache')->select();
        $or = array();
        //p($wheres);
        $componentIds = array();
        foreach ($wheres as $cClass => $where) {
            foreach ($where as $w) {
                $and = array();
                foreach ($w as $key => $val) {
                    if ($key != 'componentId') {
                        $and[] = new Vps_Model_Select_Expr_Equal($key, $val);
                    }
                }
                if (isset($w['componentId'])) {
                    $val = $w['componentId'];
                    if (!is_array($val) && strpos($val, '%') !== false) {
                        $and[] = new Vps_Model_Select_Expr_Like('component_id', $val);
                        $and[] = new Vps_Model_Select_Expr_Equal('component_class', $cClass);
                    } else {
                        // Hier keine componentClass-where, damit man im Pattern andere Komponente angeben kann
                        $and[] = new Vps_Model_Select_Expr_Equal('component_id', $val);
                    }
                } else {
                    $and[] = new Vps_Model_Select_Expr_Equal('component_class', $cClass);
                }
                $or[] = new Vps_Model_Select_Expr_And($and);
            }
        }
        $select->where(new Vps_Model_Select_Expr_Or($or));
        //p($select->getParts());
        $this->getModel()->updateRows(array('deleted' => true), $select);
        //d($this->getModel('metaModel')->getRows()->toArray());
        //d($this->getModel()->getRows()->toArray());

        // Callback
        $wheres = array();
        $wheres = $this->_addRowWhere($wheres, $row, Vps_Component_Cache_Meta_Abstract::META_TYPE_CALLBACK);
        $wheres = $this->_addModelWhere($wheres, $row, Vps_Component_Cache_Meta_Abstract::META_TYPE_CALLBACK);
        $wheres = $this->_addComponentWhere($wheres);
        $wheres = $this->_addChainedWhere($wheres);
        foreach ($this->_getComponentIdsFromWheres($wheres) as $componentId) {
            $component = Vps_Component_Data_Root::getInstance()->getComponentById(
                $componentId, array('ignoreVisible' => true)
            );
            if ($component) $component->getComponent()->onCacheCallback($row);
        }
    }

    public function cleanByModel(Vps_Model_Abstract $model)
    {
        $select = $this->getModel('metaModel')->select()
            ->whereEquals('model', get_class($model))
            ->whereNull('pattern');
        $componentClasses = array();
        foreach ($this->getModel('metaModel')->getRows($select) as $r) {
            if (call_user_func(array($r->meta_class, 'getMetaType')) == Vps_Component_Cache_Meta_Abstract::META_TYPE_DEFAULT) {
                $componentClasses[] = $r->component_class;
            }
        }
        $componentSelect = $this->getModel('metaComponent')->select()
            ->whereEquals('component_class', $componentClasses);
        foreach ($this->getModel('metaComponent')->getRows($componentSelect) as $r) {
            $componentClasses[] = $r->target_component_class;
        }
        $componentClasses = array_unique($componentClasses);
        $this->getModel('cache')->updateRows(
            array('deleted' => true),
            $this->getModel('cache')->select()->whereEquals('component_class', $componentClasses)
        );
    }

    protected function _saveMetaModel($componentClass, $modelName, $pattern, $metaClass)
    {
        $data = array(
            'model' => $modelName,
            'component_class' => $componentClass,
            'pattern' => $pattern,
            'meta_class' => $metaClass,
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaModel']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaRow(Vps_Component_Data $component, $modelName, $column, $value, $metaClass)
    {
        // TODO: checken, ob component->componentClass eh nicht schon in cache_component_meta_model mit gleichem Model steht
        $data = array(
            'model' => $modelName,
            'column' => $column,
            'value' => $value,
            'component_id' => $component->componentId,
            'component_class' => $component->componentClass,
            'meta_class' => $metaClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaRow']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaComponent(Vps_Component_Data $component, Vps_Component_Data $target)
    {
        $data = array(
            'component_id' => $component->componentId,
            'component_class' => $component->componentClass,
            'target_component_id' => $target->componentId,
            'target_component_class' => $target->componentClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaComponent']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaChained($sourceComponentClass, $targetComponentClass)
    {
        $data = array(
            'source_component_class' => $sourceComponentClass,
            'target_component_class' => $targetComponentClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaChained']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }
}
