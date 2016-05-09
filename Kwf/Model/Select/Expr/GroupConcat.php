<?php
class Kwf_Model_Select_Expr_GroupConcat implements Kwf_Model_Select_Expr_Interface
{
    /**
     * @var string
     */
    private $_field;

    /**
     * @var string
     */
    private $_separator;

    /**
     * @var string|array|null
     */
    private $_orderField;

    /**
     * The orderfield param could be the fieldname of the order field as string or an array which contains the field and the order direction
     *
     * @example
     * new Kwf_Model_Select_Expr_GroupConcat('id', ';', array(
            'field' => 'my_order_field',
     *      'direction' => 'DESC'
     * ));
     *
     * @param string $field
     * @param string $separator
     * @param string|array|null $orderField
     */
    public function __construct($field, $separator = ',', $orderField = null)
    {
        $this->setField($field);
        $this->setSeparator($separator);
        $this->setOrderField($orderField);
    }

    public function getField()
    {
        return $this->_field;
    }

    public function setField($field)
    {
        $this->_field = $field;
    }

    public function getSeparator()
    {
        return $this->_separator;
    }

    public function setSeparator($separator)
    {
        $this->_separator = $separator;
    }

    public function getOrderField()
    {
        return $this->_orderField;
    }

    public function setOrderField($orderField)
    {
        if ($orderField && !is_array($orderField)) {
            $this->_orderField = array(
                'field' => $orderField,
                'direction' => 'ASC'
            );
        } else {
            if (is_array($orderField) && (!isset($orderField['field']) || !isset($orderField['direction'] ))) {
                throw new Kwf_Exception(trlKwf('Orderfield must contain a field and a direction property!'));
            }
            $this->_orderField = $orderField;
        }
    }

    public function validate()
    {
        $this->_field->validate();
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_STRING;
    }

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Kwf_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
            'separator' => $this->_separator,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Kwf_Model_Select_Expr::fromArray($field, $data['separator']);
        }
        return new $cls($field);
    }
}
