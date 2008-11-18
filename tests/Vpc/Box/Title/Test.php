<?php
/**
 * @group Vpc_Box_Title
 */
class Vpc_Box_Title_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    private $_cache;
    public function setUp()
    {
        $this->_cache = $this->getMock('Vps_Component_Cache', array('cleanComponentClass'), array(), '', false);
        Vps_Component_Cache::setInstance($this->_cache);

        Vps_Component_Data_Root::setComponentClass('Vpc_Box_Title_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();

        Vps_Component_RowObserver::getInstance()->clear();
    }
    public function tearDown()
    {
        Vps_Component_Cache::setInstance(null);
    }

    public function testModifyTable()
    {
        $this->_cache->expects($this->once())
            ->method('cleanComponentClass')
            ->with($this->equalTo('Vpc_Box_Title_Component'));

        Vps_Model_Abstract::getInstance('Vpc_Box_Title_TableModel')
            ->getRow(1)->save();

        Vps_Component_RowObserver::getInstance()->process();
    }

    public function testModifyPage()
    {
        $this->_cache->expects($this->once())
            ->method('cleanComponentClass')
            ->with($this->equalTo('Vpc_Box_Title_Component'));

        Vps_Model_Abstract::getInstance('Vpc_Box_Title_PagesModel')
            ->getRow(1)->save();

        Vps_Component_RowObserver::getInstance()->process();
    }

    public function testModifyPageWithChildPages()
    {
        $this->_cache->expects($this->once())
            ->method('cleanComponentClass')
            ->with($this->equalTo('Vpc_Box_Title_Component'));

        Vps_Model_Abstract::getInstance('Vpc_Box_Title_PagesModel')
            ->getRow(3)->save();

        Vps_Component_RowObserver::getInstance()->process();
    }

    public function testModifyTableWithChildPages()
    {
        $this->_cache->expects($this->once())
            ->method('cleanComponentClass')
            ->with($this->equalTo('Vpc_Box_Title_Component'));

        Vps_Model_Abstract::getInstance('Vpc_Box_Title_TableModel')
            ->getRow(2)->save();

        Vps_Component_RowObserver::getInstance()->process();
    }
}
