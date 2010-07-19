<?php
/**
 * @group Component_OutputPlaceholder
 */
class Vps_Component_OutputPlaceholdersPlugin_Test extends PHPUnit_Framework_TestCase
{
    protected $_output;
    protected static $_templates = array();
    protected static $_expectedCalls = 0;
    protected static $_calls = 0;

    public function setUp()
    {
        $this->markTestIncomplete();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_OutputPlaceholdersPlugin_Root_Component');
        $this->_root = Vps_Component_Data_Root::getInstance();

        $this->_output = new Vps_Component_Output_Cache();
        Vps_Component_Cache::setBackend(Vps_Component_Cache::CACHE_BACKEND_FNF);
    }

    public function testCached()
    {
        $this->_output->renderMaster($this->_root); //caches it

        $this->assertEquals('xLorem child bar bar Ipsumy', $this->_output->renderMaster($this->_root));
    }

    public function testUncached()
    {
        $this->assertEquals('xLorem child bar bar Ipsumy', $this->_output->renderMaster($this->_root));
    }
}
