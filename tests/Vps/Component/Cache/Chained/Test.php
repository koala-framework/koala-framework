<?php
/**
 * @group Component_Cache
 * @group Component_Cache_Chained
 */
class Vps_Component_Cache_Chained_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Chained_Root');
    }

    public function testSlave()
    {
        $master = $this->_root->getChildComponent('-master');
        $slave = $this->_root->getChildComponent('-slave');

        $this->assertEquals('foo', $master->render());
        $this->assertEquals('foo', $slave->render());
        $row = Vps_Model_Abstract::getInstance('Vps_Component_Cache_Chained_Master_Model')
            ->getRow('root-master');
        $row->value = 'bar';
        $row->save();
        $this->_process();
        $this->assertEquals('bar', $master->render());
        $this->assertEquals('bar', $slave->render());
    }

    public function testDbId()
    {
        $master = $this->_root->getChildComponent('-master')->getChildComponent('_1');
        $slave = $this->_root->getChildComponent('-slave')->getChildComponent('_1');

        $this->assertEquals('foo', $master->render());
        $this->assertEquals('foo', $slave->render());
        $row = Vps_Model_Abstract::getInstance('Vps_Component_Cache_Chained_Master_ChildModel')
            ->getRow(1);
        $row->value = 'bar';
        $row->save();
        $this->_process();
        $this->assertEquals('bar', $master->render());
        $this->assertEquals('bar', $slave->render());
    }
}