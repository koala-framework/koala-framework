<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_Date_Year implements Kwf_Model_Select_Expr_Interface
{
    const FORMAT_DIGITS_TWO = 'y';
    const FORMAT_DIGITS_FOUR = 'Y';
    private $_field;
    private $_format;

    public function __construct($field, $format = Kwf_Model_Select_Expr_Date_Year::FORMAT_DIGITS_FOUR)
    {
        $this->_format = $format;
        $this->_field = $field;
    }

    public function getField()
    {
        return $this->_field;
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

    public function getFormat()
    {
        return $this->_format;
    }

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
            'format' => $this->_format,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Kwf_Model_Select_Expr::fromArray($field);
        }
        return new $cls($field, $data['format']);
    }
}
