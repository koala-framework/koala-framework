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
        $this->assertRegExp('#x.*Lorem child bar bar Ipsum.*y#s', $this->_root->render(true, true));

        $this->assertRegExp('#x.*Lorem child bar bar Ipsum.*y#s', $this->_root->render(true, true));
    }
}
