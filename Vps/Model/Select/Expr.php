<?php
class Vps_Model_Select_Expr
{
    public static function fromArray(array $data)
    {
        $cls = 'Vps_Model_Select_Expr_'.$data['exprType'];
        return call_user_func(array($cls, 'fromArray'), $data);
    }
}
