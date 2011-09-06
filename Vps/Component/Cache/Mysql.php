<?php
class Vps_Component_Cache_Mysql extends Vps_Component_Cache
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Vps_Component_Cache_Mysql_Model',
            'url' => 'Vps_Component_Cache_Mysql_UrlModel',
            'urlParents' => 'Vps_Component_Cache_Mysql_UrlParentsModel',
        );
    }

    /**
     * @return Vps_Model_Abstract
     */
    public function getModel($type = 'cache')
    {
        if (!isset($this->_models[$type])) return null;
        if (is_string($this->_models[$type])) {
            $this->_models[$type] = Vps_Model_Abstract::getInstance($this->_models[$type]);
        }
        return $this->_models[$type];
    }

    public function save(Vps_Component_Data $component, $content, $type = 'component', $value = '')
    {
        $settings = $component->getComponent()->getViewCacheSettings();
        if ($type != 'componentLink' && $type != 'master' && $type != 'page' && !$settings['enabled']) {
            $content = self::NO_CACHE;
        }

        // MySQL
        $data = array(
            'component_id' => (string)$component->componentId,
            'db_id' => (string)$component->dbId,
            'component_class' => $component->componentClass,
            'type' => $type,
            'value' => (string)$value,
            'expire' => is_null($settings['lifetime']) ? null : time() + $settings['lifetime'],
            'deleted' => false,
            'content' => $content
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->getModel('cache')->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);

        // APC
        $cacheId = $this->_getCacheId($component->componentId, $type, $value);
        $ttl = 0;
        if ($settings['lifetime']) $ttl = $settings['lifetime'];
        apc_add($cacheId, $content, $ttl);

        return true;
    }

    public function load($componentId, $type = 'component', $value = '')
    {
        if ($componentId instanceof Vps_Component_Data) {
            $componentId = $componentId->componentId;
        }
        $cacheId = $this->_getCacheId($componentId, $type, $value);
        $content = apc_fetch($cacheId);
        if ($content === false) {
            Vps_Benchmark::count('comp cache mysql');
            $select = $this->getModel('cache')->select()
                ->whereEquals('component_id', $componentId)
                ->whereEquals('type', $type)
                ->whereEquals('deleted', false)
                ->whereEquals('value', $value)
                ->where(new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_Higher('expire', time()),
                    new Vps_Model_Select_Expr_IsNull('expire'),
                )));
            $row = $this->getModel('cache')->export(Vps_Model_Db::FORMAT_ARRAY, $select);
            $content = isset($row[0]) ? $row[0]['content'] : null;
            if (isset($row[0])) {
                $ttl = 0;
                if ($row[0]['expire']) $ttl = $row[0]['expire']-time();
                apc_add($cacheId, $content, $ttl);
            }
        }
        return $content;
    }

    public function deleteViewCache($select)
    {
        $model = $this->getModel();
        foreach ($model->export(Vps_Model_Abstract::FORMAT_ARRAY, $select) as $row) {
            $cacheId = $this->_getCacheId($row['component_id'], 'component', null);
            apc_delete($cacheId);
        }
        $model->updateRows(array('deleted' => true), $select);
    }

    protected static function _getCacheId($componentId, $type, $value)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix() . '-cc-';
        return $prefix . "$componentId/$type/$value";
    }

    // wird nur von Vps_Component_View_Renderer->saveCache() verwendet
    public function test($componentId, $type = 'component', $value = '')
    {
        return !is_null($this->load($componentId, $type, $value));
    }

/*
    protected function _cleanUrl(Vps_Component_Data $component)
    {
        $ids[] = $component->componentId;

        $s = new Vps_Model_Select();
        $s->whereEquals('parent_page_id', $component->componentId);
        foreach ($this->getModel('urlParents')->export(Vps_Model_Abstract::FORMAT_ARRAY, $s) as $r) {
            $ids[] = $r['page_id'];
        }

        $s = new Vps_Model_Select();
        $s->whereEquals('page_id', $ids);
        foreach ($this->getModel('url')->export(Vps_Model_Abstract::FORMAT_ARRAY, $s) as $row) {
            static $prefix;
            if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
            $cacheId = $prefix.'url-'.$row['url'];
            apc_delete($cacheId);
        }
        $this->getModel('url')->deleteRows($s);
    }

    protected function _cleanProcessInput(Vps_Component_Data $component)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        $cacheId = $prefix.'procI-'.$component->getPageOrRoot()->componentId;
        apc_delete($cacheId);
    }
    */
}
