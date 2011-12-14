<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr
{
    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return call_user_func(array($cls, 'fromArray'), $data);
    }
}
