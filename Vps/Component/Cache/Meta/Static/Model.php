<?php
class Vps_Component_Cache_Meta_Static_Model extends Vps_Component_Cache_Meta_Static_Abstract
{
    public function __construct($model, $pattern = null)
    {
        $this->_model = $model;
        if ($pattern) $this->_pattern = $pattern;

        $model = $this->_getModel($model);
        $pattern = $this->getPattern();
        $matches = array();
        preg_match_all('/\{([a-z0-9_]+)\}/', $pattern, $matches);
        foreach ($matches[1] as $m) {
            //if (!$model->hasColumn($m)) throw new Vps_Exception("Model must have column '$m' for pattern '$pattern'");
        }
    }
}