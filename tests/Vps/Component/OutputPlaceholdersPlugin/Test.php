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
        $this->_output->getCache()->setModel(new Vps_Component_Cache_CacheModel());
        $this->_output->getCache()->setMetaModel(new Vps_Component_Cache_CacheMetaModel());
        $this->_output->getCache()->setFieldsModel(new Vps_Component_Cache_CacheFieldsModel());
        $this->_output->getCache()->emptyPreload();
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
