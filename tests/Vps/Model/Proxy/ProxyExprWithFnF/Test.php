<?php
/**
 * @group Model
 * @group Model_ProxyExprWithFnF
 */
class Vps_Model_Proxy_ProxyExprWithFnF_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $model = new Vps_Model_Proxy(array(
            'proxyModel' => new Vps_Model_FnF(),
            'exprs' => array(
                'foo' => new Vps_Model_Select_Expr_String('bar')
            )
        ));
        $row = $model->createRow();
        $row->xy = 'xy';
        $row->save();

        $this->assertEquals('bar', $row->foo);
    }
}
