<?php
abstract class Vps_Component_View_Renderer
{
    protected $_view;

    public function setView(Vps_View $view)
    {
        $this->_view = $view;
    }

    /**
     * @return Vps_View
     */
    protected function _getView()
    {
        return $this->_view;
    }

    public abstract function render($component, $config, $view);

    protected function _getCacheValue()
    {
        return '';
    }

    public function saveCache($component, $content) {
        $type = strtolower(substr(strrchr(get_class($this), '_'), 1));
        $ret = Vps_Component_Cache::getInstance()->save(
            $component,
            $content,
            $type,
            $this->_getCacheValue()
        );
        if ($ret) {
            //$this->_saveMeta($this->_getCacheVars($component));
            //$this->_saveMeta($component);
        }
        return $ret;
    }
/*
    protected function _saveMeta() {}


    protected function _getCacheVars($component)
    {
        return array();
    }

    protected final function _saveMeta($meta)
    {
        if (!is_array($meta)) $meta = array($meta);
        foreach ($meta as $m) {
            $modelname = null;
            $fieldname = null;
            $value = null;
            if (is_array($m)) {
                if (isset($m['row'])) {

                }
            }
            if (is_object($m)) {
                if ($m instanceof Vps_Model_Row_Abstract) {
                    $model = $m->getModel();
                    if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
                } else if ($m instanceof Zend_Db_Table_Row_Abstract) {
                    $model = $m->getTable();
                } else {
                    throw new Vps_Exception('getCacheVars for ' . $component->componentClass . ' must deliver row, got "' . get_class($m) . '"');
                }
            } else if (is_array($m)) {
                $model = Vps_Model_Abstract::getInstance($m['model']);
                if (!$model) throw new Vps_Exception()
            }
            if (!isset($m['model'])) throw new Vps_Exception('getCacheVars for ' . $component->componentClass . ' must deliver model');
            $model = $m['model'];
            $id = isset($m['id']) ? $m['id'] : null;
            if (isset($m['callback']) && $m['callback']) {
                $type = Vps_Component_Cache::META_CALLBACK;
                $value = $componentId;
            } else {
                $type = Vps_Component_Cache::META_CACHE_ID;
                $value = $cacheId;
            }
            if (isset($m['componentId'])) {
                $value = $this->getCache()->getCacheId($m['componentId']);
            }
            $field = isset($m['field']) ? $m['field'] : '';
            $this->getCache()->saveMeta($model, $id, $value, $type, $field);
        }
    }
    */
}
