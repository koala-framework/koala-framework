<?php
abstract class Vps_Component_Cache_Meta_Static_Abstract extends Vps_Component_Cache_Meta_Abstract
{
    protected $_pattern;
    protected $_params;

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

    public function setDirtyColumns($columns)
    {
        $this->_params['dirtyColumns'] = $columns;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        if (isset($params['dirtyColumns'])) {
            if (!array_intersect($dirtyColumns, $params['dirtyColumns'])) {
                return null;
            }
        }

        $ret = array();
        $dbId = $pattern;
        $ret['type'] = array('component', 'master', 'mail');
        if (!$dbId) return $ret;
        $matches = array();
        preg_match_all('/\{([a-z0-9_]+)\}/', $dbId, $matches);
        foreach ($matches[1] as $m) {
            $dbId = str_replace('{' . $m . '}', $row->$m, $dbId);
        }
        $ret['db_id'] = $dbId;
        return $ret;
    }
}