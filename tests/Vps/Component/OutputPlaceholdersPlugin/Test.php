<?php
/**
 * @group Component_OutputPlaceholder
 */
class Vps_Component_OutputPlaceholdersPlugin_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_OutputPlaceholdersPlugin_Root_Component');
    }

    public function testCached()
    {
        $this->assertEquals('xLorem child bar bar Ipsumy', $this->_root->render(true, true));
    }
}
