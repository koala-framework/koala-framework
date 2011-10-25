<?php
/**
 * @group Component_Cache_OwnModel
 */
class Kwf_Component_Cache_OwnModel_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_OwnModel_Root_Component');
    }

    public function testOwnModel()
    {
        $this->assertEquals('foo', $this->_root->render());

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_OwnModel_Root_Model')
            ->getRow('root');
        $row->content = 'bar';
        $row->save();

        $this->_process();
        $this->assertEquals('bar', $this->_root->render());
    }
}
