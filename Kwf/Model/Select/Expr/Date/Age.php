<?php
/**
 * calculate the age of a row in a model from $field to now or optionally to $_referenceDate
 */
class Kwf_Model_Select_Expr_Date_Age implements Kwf_Model_Select_Expr_Interface
{
    private $_field;

    /**
     * @var Kwf_Date
     */
    private $_referenceDate;

    public function __construct($birthDateField, Kwf_Date $referenceDate = null)
    {
        $this->_field = $birthDateField;
        if ($referenceDate) {
            $this->_referenceDate = $referenceDate;
        } else {
            $this->_referenceDate = new Kwf_Date('now');
        }
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getDate()
    {
        return $this->_referenceDate;
    }

    public function validate()
    {
        if (!$this->_field) {
            throw new Kwf_Exception("No Field set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_INTEGER;
    }


    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $this->_field,
            'date' => $this->_referenceDate,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['field'], $data['calculateDateTo']);
    }
}
