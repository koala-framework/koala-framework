<?php
class Vps_Validate_Unique extends Zend_Validate_Abstract
{
    public $fieldName;
    public $model;
    const NOT_UNIQUE = 'notUnique';

    //TODO: ene möglichkeit schaffen mit der die Form-Felder den Validatoren
    //fieldName + model (ev. row) übergeben können
    public function __construct($fieldName, Vps_Model_Interface $model)
    {
        $this->fieldName = (string)$fieldName;
        $this->model = $model;
        $this->_messageTemplates[self::NOT_UNIQUE] = trlVps("'%value%' does allready exist");
    }

    public function isValid($value)
    {
        $valueString = (string)$value;

        $this->_setValue($valueString);

        if ($this->model->fetchAll(array("$this->fieldName = ?"=>$valueString))->current()) {
            $this->_error(self::NOT_UNIQUE);
            return false;
        }

        return true;
    }
}
