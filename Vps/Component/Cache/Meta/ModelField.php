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
        $parts = preg_split('/([_\-])/', $this->_value, -1, PREG_SPLIT_DELIM_CAPTURE);
        d($parts);
        while ($component) {
            if (isset($component->generator)) {
                p($component->componentId . ' - ' . get_class($component->generator));
            }
            if (get_class($component->generator) == 'Vpc_Root_Category_Generator') break;
            $component = $component->parent;
        }
        /*
        $generator = $component->getGenerator('paragraphs');
        if ($generator instanceof Vps_Component_Generator_Table) {

        }
        */

        return $this->_value;
    }
}