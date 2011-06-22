<?php
/**
 * @group vpsLoader
 */
class Vps_Loader_Test extends Vps_Test_TestCase
{
    public function testClassExistsExisting()
    {
        $this->assertTrue(class_exists('Vps_Loader_TestClass'));
    }

    public function testClassExistsNotExisting()
    {
        $this->assertFalse(class_exists('Vps_Loader_TestClassNotExisting'));
    }

    public function testInstanciateClass()
    {
        new Vps_Loader_TestClass2();
    }

    /* macht einen fatal error, ist somit untestbar
     * ...hab ich schon erwäht dass php scheiße ist?
    public function testInstanciateClassNotExisting()
    {
        new Vps_Loader_TestClassNotExisting();
    }
    */
}
