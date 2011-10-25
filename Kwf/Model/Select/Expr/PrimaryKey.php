<?php
class Kwf_Model_Select_Expr_PrimaryKey implements Kwf_Model_Select_Expr_Interface
{
    public function validate()
    {
    }

    public function getResultType()
    {
        return null;
    }

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls();
    }
}
