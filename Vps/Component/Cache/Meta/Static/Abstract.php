<?php
abstract class Vps_Component_Cache_Meta_Static_Abstract extends Vps_Component_Cache_Meta_Abstract
{
    protected $_pattern;

    public function __construct($pattern)
    {
        $this->_pattern = $pattern;
    }

    public function getModelname($componentClass)
    {
        return null;
    }

    public function getPattern()
    {
        return $this->_pattern;
    }

    public static function createComponentId($pattern, $row)
    {
        $componentId = $pattern;
        if (!$componentId) return null;
        $matches = array();
        preg_match_all('/\{([a-z0-9_]+)\}/', $componentId, $matches);
        foreach ($matches[1] as $m) {
            $componentId = str_replace('{' . $m . '}', $row->$m, $componentId);
        }
        return $componentId;
    }
}