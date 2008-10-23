<?php
/**
 * @group Component_Cache
 */
class Vps_Component_Cache_ClearWithComponentId_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    private $_cache;
    public function setUp()
    {
        $this->_cache = $this->getMock('Vps_Component_Cache', array('remove'), array(), '', false);
        Vps_Component_Cache::setInstance($this->_cache);

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_ClearWithComponentId_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
    public function tearDown()
    {
        Vps_Component_Cache::setInstance(null);
    }

    public function testClear()
    {
        Zend_Registry::get('config')->hasIndex = false;

        $this->_cache->expects($this->once())
            ->method('remove')
            ->with($this->equalTo(array($this->_root->getComponentById('root-child'))));

        $model = Vpc_Abstract::createModel('Vps_Component_Cache_ClearWithComponentId_Html');
        $row = $model->getRow('root-child');
        $row->content = 'blub';
        $row->save();

        Vps_Component_RowObserver::getInstance()->process(false);
    }
}
