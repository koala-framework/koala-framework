<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRowsWithMirrorCacheSimple
 */
class Vps_Model_Mongo_ChildRowsWithMirrorCacheSimple_Test extends Vps_Test_TestCase
{
    public function testIt()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel');

        $m->initialSync();

        $this->assertEquals(1, $m->getProxyModel()->getCollection()->find()->count());
        $row = $m->getProxyModel()->getCollection()->findOne();
        $this->assertEquals('bar', $row['foo']);
        $this->assertEquals(1, count($row['children']));
        $this->assertEquals('blub', $row['children'][0]['blub']);
    }

    public function testParentExpr()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel');
        $m->initialSync();
        $row = $m->getRow(1);
        $this->assertEquals('bar', $row->getChildRows('Children')->current()->parent_foo);
    }

    public function testParentExprIsntCached()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel');

        $m->initialSync();
        $row = $m->getProxyModel()->getCollection()->findOne();
        $this->assertTrue(!isset($row['children'][0]['parent_foo']));
    }
}