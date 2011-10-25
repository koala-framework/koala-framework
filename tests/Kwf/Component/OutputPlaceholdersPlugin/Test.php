<?php
/**
 * @group Component_OutputPlaceholder
 */
class Kwf_Component_OutputPlaceholdersPlugin_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_OutputPlaceholdersPlugin_Root_Component');
    }

    public function testCached()
    {
        $this->assertEquals('xLorem child bar bar Ipsumy', $this->_root->render(true, true));

        $this->assertEquals('xLorem child bar bar Ipsumy', $this->_root->render(true, true));
    }
}
