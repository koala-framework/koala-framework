<?php
/**
 * @group Component_Cache
 */
class Vps_Component_Cache_ClearMenu_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    private $_cache;
    public function setUp()
    {
        $this->_cache = $this->getMock('Vps_Component_Cache', array('remove', 'cleanComponentClass'), array(), '', false);
        Vps_Component_Cache::setInstance($this->_cache);

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_ClearMenu_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
    public function tearDown()
    {
        Vps_Component_Cache::setInstance(null);
    }

    public function testLinkTag()
    {
        Vps_Component_RowObserver::getInstance()->clear();

        $this->_cache->expects($this->atLeastOnce()) // wird wegen proxy 2x aufgerufen
            ->method('cleanComponentClass')
            ->with('Vpc_Menu_Component');

        $model = Vpc_Abstract::createModel('Vps_Component_Cache_ClearMenu_Link');
        $row = $model->getRow('1');
        $row->save();

        Vps_Component_RowObserver::getInstance()->process();
    }

    public function testLinkIntern()
    {
        Vps_Component_RowObserver::getInstance()->clear();

        $this->_cache->expects($this->atLeastOnce()) // wird wegen proxy 2x aufgerufen
            ->method('cleanComponentClass')
            ->with('Vpc_Menu_Component');

        $model = Vpc_Abstract::createModel('Vps_Component_Cache_ClearMenu_LinkIntern');
        $row = $model->getRow('1-link');
        $row->save();

        Vps_Component_RowObserver::getInstance()->process();
    }
}
