<?php
/**
 * @group Component_Cache_ComponentLink
 */
class Vps_Component_Cache_ComponentLink_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_ComponentLink_Root_Component');
    }

    public function testRename()
    {
        $root = $this->_root;

        $this->assertEquals(1, substr_count($root->render(), 'F1'));

        $cache = Vps_Component_Cache::getInstance();

        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_ComponentLink_Root_Model');
        $row = $model->getRow(1);
        $row->name = 'G1';
        $row->save();
        $this->assertEquals(1, substr_count($root->render(), 'F1')); // checken ob richtig preloaded wird

        $this->_process();
        $this->assertEquals(0, substr_count($root->render(), 'F1'));
        $this->assertEquals(1, substr_count($root->render(), 'G1'));
    }

    public function testMove()
    {
        $root = $this->_root;

        $this->assertEquals(3, substr_count($root->render(), 'f1'));
        $this->assertEquals(2, substr_count($root->render(), 'f1/f2'));
        $this->assertEquals(1, substr_count($root->render(), 'f1/f2/f3'));
        $this->assertEquals(1, substr_count($root->render(), 'f4'));

        $cache = Vps_Component_Cache::getInstance();

        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_ComponentLink_Root_Model');
        $row = $model->getRow(2);
        $row->parent_id = 4;
        $row->save();
        $this->_process();

        $this->assertEquals(1, substr_count($root->render(), 'f1'));
        $this->assertEquals(2, substr_count($root->render(), 'f4/f2'));
        $this->assertEquals(1, substr_count($root->render(), 'f4/f2/f3'));
        $this->assertEquals(1, substr_count($root->render(), 'f3'));
    }
}
