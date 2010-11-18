<?php
/**
 * @group Vpc_Trl
 * @group Vpc_InheritContent
 */
class Vpc_Trl_InheritContent_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_InheritContent_Root');
    }

    public function testHasContentDe()
    {
        $this->assertTrue($this->_root->getComponentById('root-de-box-child')->getComponent()->hasContent());
        $this->assertFalse($this->_root->getComponentById('root-de_test-box-child')->getComponent()->hasContent());
        $this->assertFalse($this->_root->getComponentById('root-de_test_test2-box-child')->getComponent()->hasContent());
        $this->assertTrue($this->_root->getComponentById('root-de_test_test2_test3-box-child')->getComponent()->hasContent());
    }

    public function testContentDe()
    {
        $this->assertEquals('root-de-box-child',
            $this->_root->getComponentById('root-de-box-child')->render());
        $this->assertEquals('root-de_test_test2_test3-box-child',
            $this->_root->getComponentById('root-de_test_test2_test3-box-child')->render());

        $this->assertEquals('root-de-box-child',
            $this->_root->getComponentById('root-de-box')->render());
        $this->assertEquals('root-de-box-child',
            $this->_root->getComponentById('root-de_test-box')->render());
        $this->assertEquals('root-de-box-child',
            $this->_root->getComponentById('root-de_test_test2-box')->render());
        $this->assertEquals('root-de_test_test2_test3-box-child',
            $this->_root->getComponentById('root-de_test_test2_test3-box')->render());
    }

    public function testContentEn1()
    {
        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en-box-child')->render());
        $this->assertEquals('root-en_test_test2_test3-box-child',
            $this->_root->getComponentById('root-en_test_test2_test3-box-child')->render());
    }

    public function testContentEn2()
    {
        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en-box')->render());
        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en_test-box')->render());
        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en_test_test2-box')->render());
    }

    public function testContentEn3()
    {
        //test3-box has no content in en
        $this->assertEquals('root-en-box-child',
            $this->_root->getComponentById('root-en_test_test2_test3-box')->render());
    }
}
