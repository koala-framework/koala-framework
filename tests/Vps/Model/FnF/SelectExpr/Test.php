<?php
/**
 * @group Model
 * @group Model_FnF
 * @group Model_FnF_SelectExpr
 */
class Vps_Model_FnF_SelectExpr_Test extends PHPUnit_Framework_TestCase
{
    public function testWithoutExpr()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_FnF_SelectExpr_Model1');

        $s = $m1->select();
        $s->order('id');
        $row = $m1->getRow($s);
        $this->assertEquals($row->id, 1);
        $this->assertEquals($row->count_model2, 2);

        $row->foo = 'a';
        $row->save();
    }
}
