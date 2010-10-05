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

    protected function _addRowComponentIds($componentIds, $row, $callback = false)
    {
        // Das suchen wir
        // $searchModel = model, column, value, component_id, callback
        // $searchModel->model = $row->model && $searchModel->value=$row->{$searchModel->column}
        $searchModel = $this->getModel('metaRow');

        $select = $searchModel->select()
            ->whereEquals('model', $this->_getModelname($row))
            ->whereEquals('callback', $callback);

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
            foreach ($searchModel->getRows($select) as $r) {
                $componentIds[$r->component_class][$r->component_id] = true;
            }
        }
        return $componentIds;
    }

    protected function _addModelComponentIds($componentIds, $row, $callback = false)
    {
        $model = $this->getModel('metaModel');

        $select = $model->select()
            ->whereEquals('model', $this->_getModelname($row))
            ->whereEquals('callback', $callback);

        foreach ($model->getRows($select) as $metaRow) {
            $componentId = call_user_func(
                array($metaRow->meta_class, 'createComponentId'),
                $metaRow->pattern, $row
            );
            $componentIds[$metaRow->component_class][$componentId] = true;
        }
        return $componentIds;
    }

    protected function _addMetaComponentIds($componentIds)
    {
        // $model: component_id component_class target_component_id target_component_class
        // So lange $model nach component_id durchsuchen und in ergebnis
        // $model->target_component_id reingeben bis nichts mehr neues kommt
        $ids = array();
        foreach ($componentIds as $k => $c) $ids = array_merge($ids, array_keys($c));
        $model = $this->getModel('metaComponent');
        do {
            $select = $model->select();
            $select->whereEquals('component_id', $ids);
            $ids2 = array();
            foreach ($model->getRows($select) as $r) {
                $ids2[] = $r->target_component_id;
                $componentIds[$r->component_class][$r->target_component_id] = true;
            }
            $ids2 = array_diff($ids2, $ids);
            $ids = array_unique(array_merge($ids, $ids2));
        } while ($ids2);
        return $componentIds;
    }

    protected function _addChainedComponentIds($ret)
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
            ->whereEquals('source_component_class', array_keys($ret));
        foreach ($model->getRows($select) as $row) { // Alle infrage kommenden target_component_classes

            if (!isset($ret[$row->source_component_class])) continue;

            foreach ($ret[$row->source_component_class] as $componentId => $null) { // Alle master-componentIds der target_component_class

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
                $c = $component;
                while ($c) {

                    $chainedType = Vpc_Abstract::getFlag($c->componentClass, 'chainedType');
                    if ($chainedType && isset($chainedTypes[$chainedType])) {

                        $chainedComponents = $c->parent->getChildComponents(array(
                            'componentClass' => $chainedTypes[$chainedType],
                            'ignoreVisible' => true
                        ));
                        foreach ($chainedComponents as $chainedComponent) {
                            $part2 = substr($componentId, strlen($c->componentId));
                            $componentId = $chainedComponent->componentId . $part2;
                            $ret[$chainedComponent->componentClass][$componentId] = true;
                        }
                    }

                    $c = $c->parent;
                }
            }

        }
        return $ret;
    }

    public function cleanByRow(Vps_Model_Row_Abstract $row)
    {
        $this->cleanByModel($row->getModel());

        $ids = array();
        $ids = $this->_addRowComponentIds($ids, $row);
        $ids = $this->_addModelComponentIds($ids, $row);
        $ids = $this->_addMetaComponentIds($ids);
        $ids = $this->_addChainedComponentIds($ids);
        $or = array();
        $componentIds = array();
        foreach ($ids as $cClass => $cIds) {
            foreach (array_keys($cIds) as $cId) {
                if (strpos($cId, '%') !== false) {
                    $or[] = new Vps_Model_Select_Expr_And(array(
                        new Vps_Model_Select_Expr_Like('component_id', $cId),
                        new Vps_Model_Select_Expr_Equal('component_class', $cClass)
                    ));
                } else {
                    $componentIds[] = $cId;
                }
            }
        }

        $select = $this->getModel('cache')->select();
        $or[] = new Vps_Model_Select_Expr_Equal('component_id', $componentIds);
        $select->where(new Vps_Model_Select_Expr_Or($or));
        $this->getModel()->updateRows(array('deleted' => true), $select);

        // Callback
        $ids = array();
        $ids = $this->_addRowComponentIds($ids, $row, true);
        $ids = $this->_addModelComponentIds($ids, $row, true);
        $ids = $this->_addMetaComponentIds($ids);
        $ids = $this->_addChainedComponentIds($ids);
        foreach ($ids as $componentIds) {
            foreach (array_unique($componentIds) as $componentId => $null) {
                $component = Vps_Component_Data_Root::getInstance()->getComponentById(
                    $componentId, array('ignoreVisible' => true)
                );
                if ($component) $component->getComponent()->onCacheCallback($row);
            }
        }
    }

    public function cleanByModel(Vps_Model_Abstract $model, $callback = false)
    {
        $select = $this->getModel('metaModel')->select()
            ->whereEquals('model', get_class($model))
            ->whereNull('pattern')
            ->whereEquals('callback', $callback);
        $componentClasses = array();
        foreach ($this->getModel('metaModel')->getRows($select) as $r) {
            $componentClasses[] = $r->component_class;
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

    protected function _saveMetaModel($componentClass, $modelName, $pattern, $isCallback, $metaClass)
    {
        $data = array(
            'model' => $modelName,
            'component_class' => $componentClass,
            'pattern' => $pattern,
            'callback' => $isCallback,
            'meta_class' => $metaClass,
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaModel']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaRow(Vps_Component_Data $component, $modelName, $column, $value, $isCallback)
    {
        // TODO: checken, ob component->componentClass eh nicht schon in cache_component_meta_model mit gleichem Model steht
        $data = array(
            'model' => $modelName,
            'column' => $column,
            'value' => $value,
            'component_id' => $component->componentId,
            'component_class' => $component->componentClass,
            'callback' => $isCallback
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models[$type]->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
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
