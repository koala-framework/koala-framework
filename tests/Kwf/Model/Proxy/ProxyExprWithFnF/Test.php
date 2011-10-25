<?php
/**
 * @group Model
 * @group Model_ProxyExprWithFnF
 */
class Kwf_Model_Proxy_ProxyExprWithFnF_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $model = new Kwf_Model_Proxy(array(
            'proxyModel' => new Kwf_Model_FnF(),
            'exprs' => array(
                'foo' => new Kwf_Model_Select_Expr_String('bar')
            )
        ));
        $row = $model->createRow();
        $row->xy = 'xy';
        $row->save();

        $this->assertEquals('bar', $row->foo);
    }
}
