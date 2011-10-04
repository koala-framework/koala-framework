<?php
/**
 * @group Component_Cache_HasContent
 */
class Vps_Component_Cache_HasContent_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_HasContent_Root_Component');
    }

    public function testHasContent()
    {
        $row = $this->_root
            ->getChildComponent('_child')->getComponent()
            ->getOwnModel()
            ->getRow('root_child');

        $this->assertEquals('', $this->_root->render());

        $row->has_content = true;
        $row->save();
        $this->_process();
        $this->assertEquals('1', $this->_root->render());

        $row->has_content = false;
        $row->save();
        $this->_process();
        $this->assertEquals('', $this->_root->render());
    }
}
