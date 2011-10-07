<?php
/**
 * @group kwfLoader
 */
class Kwf_Loader_Test extends Kwf_Test_TestCase
{
    public function testClassExistsExisting()
    {
        $this->assertTrue(class_exists('Kwf_Loader_TestClass'));
    }

    public function testClassExistsNotExisting()
    {
        $this->assertFalse(class_exists('Kwf_Loader_TestClassNotExisting'));
    }

    public function testInstanciateClass()
    {
        new Kwf_Loader_TestClass2();
    }

    /* macht einen fatal error, ist somit untestbar
     * ...hab ich schon erwäht dass php scheiße ist?
    public function testInstanciateClassNotExisting()
    {
        new Kwf_Loader_TestClassNotExisting();
    }
    */
}
