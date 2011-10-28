<?php
class Kwf_Component_Cache_Mysql extends Kwf_Component_Cache
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Kwf_Component_Cache_Mysql_Model',
            'url' => 'Kwf_Component_Cache_Mysql_UrlModel',
        );
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public function getModel($type = 'cache')
    {
        if (!isset($this->_models[$type])) return null;
        if (is_string($this->_models[$type])) {
            $this->_models[$type] = Kwf_Model_Abstract::getInstance($this->_models[$type]);
        }
        return $this->_models[$type];
    }

    public function save(Kwf_Component_Data $component, $content, $type = 'component', $value = '')
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
        $this->getModel('cache')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array($data), $options);

        // APC
        $cacheId = $this->_getCacheId($component->componentId, $type, $value);
        $ttl = 0;
        if ($settings['lifetime']) $ttl = $settings['lifetime'];
        Kwf_Cache_Simple::add($cacheId, $content, $ttl);

        return true;
    }

    public function load($componentId, $type = 'component', $value = '')
    {
        if ($componentId instanceof Kwf_Component_Data) {
            $componentId = $componentId->componentId;
        }
        $cacheId = $this->_getCacheId($componentId, $type, $value);
        $content = Kwf_Cache_Simple::fetch($cacheId);
        if ($content === false) {
            Kwf_Benchmark::count('comp cache mysql');
            $select = $this->getModel('cache')->select()
                ->whereEquals('component_id', $componentId)
                ->whereEquals('type', $type)
                ->whereEquals('deleted', false)
                ->whereEquals('value', $value)
                ->where(new Kwf_Model_Select_Expr_Or(array(
                    new Kwf_Model_Select_Expr_Higher('expire', time()),
                    new Kwf_Model_Select_Expr_IsNull('expire'),
                )));
            $row = $this->getModel('cache')->export(Kwf_Model_Db::FORMAT_ARRAY, $select);
            $content = isset($row[0]) ? $row[0]['content'] : null;
            if (isset($row[0])) {
                $ttl = 0;
                if ($row[0]['expire']) $ttl = $row[0]['expire']-time();
                Kwf_Cache_Simple::add($cacheId, $content, $ttl);
            }
        }
        return $content;
    }

    public function deleteViewCache($select)
    {
        $model = $this->getModel();
        foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select) as $row) {
            $cacheId = $this->_getCacheId($row['component_id'], $row['type'], null);
            Kwf_Cache_Simple::delete($cacheId);
        }
        $model->updateRows(array('deleted' => true), $select);
    }

    protected static function _getCacheId($componentId, $type, $value)
    {
        return "cc-$componentId/$type/$value";
    }

    // wird nur von Kwf_Component_View_Renderer->saveCache() verwendet
    public function test($componentId, $type = 'component', $value = '')
    {
        return !is_null($this->load($componentId, $type, $value));
    }
}
