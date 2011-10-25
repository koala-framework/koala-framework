<?php
/**
 * @group Kwc_Trl
 * @group Kwc_InheritContentWithVisible
 */
class Kwc_Trl_InheritContentWithVisible_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_InheritContentWithVisible_Root');
    }

    public function testContentEn3()
    {
        /*
        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en-box')->render());

        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en_test-box')->render());

        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en_test_test2-box')->render());
        */
        $this->assertEquals('root-en_test_test2_test3-box-child',
            $this->_root->getComponentById('root-en_test_test2_test3-box')->render());
    }
}
