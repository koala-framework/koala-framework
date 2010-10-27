<?php
class Vps_Component_Cache_Meta_ModelField extends Vps_Component_Cache_Meta_Abstract
{
    private $_model;
    private $_column;
    private $_field;

    public function __construct($model, $column, $value)
    {
        $model = $this->_getModel($model);
        if (!$model->hasColumn($column)) {
            throw new Vps_Exception('Model "' . get_class($model) . '" must have column "' . $column . '"');
        }
        $this->_model = $model;
        $this->_column = $column;
        $this->_value = $value;
    }

    public function getColumn()
    {
        return $this->_column;
    }

    public function getValue(Vps_Component_Data $component)
    {
        return $this->_value;
    }

    public function getModelname()
    {
        return $this->_getModelname($this->_model);
    }

    public static function getDeleteWhere($dbId)
    {
        return array(
            'type' => array('component', 'box', 'master', 'partials'),
            'db_id' => $dbId
        );
    }
}