<?php
/**
 * @group Component_Cache_OwnModel
 */
class Vps_Component_Cache_OwnModel_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_OwnModel_Root_Component');
    }

    public function testOwnModel()
    {
        $this->assertEquals('foo', $this->_root->render());

        $row = Vps_Model_Abstract::getInstance('Vps_Component_Cache_OwnModel_Root_Model')
            ->getRow('root');
        $row->content = 'bar';
        $row->save();

        $this->_process();
        $this->assertEquals('bar', $this->_root->render());
    }
}
