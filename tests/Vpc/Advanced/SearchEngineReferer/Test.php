<?php
/**
 * @group Vpc_Advanced_Referer
 */
class Vpc_Advanced_SearchEngineReferer_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    private $_cache;

    public function setUp()
    {
        /*
        $this->_cache = $this->getMock('Vps_Component_Cache', array('remove'), array(), '', false);
        Vps_Component_Cache::setInstance($this->_cache);

        Vps_Component_Data_Root::setComponentClass('Vpc_Advanced_SearchEngineReferer_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();

        Vps_Component_RowObserver::getInstance()->clear();
        */
    }

    public function tearDown()
    {
        $this->markTestIncomplete();
        Vps_Component_Cache::setInstance(null);
        if (isset($_SERVER['HTTP_REFERER'])) unset($_SERVER['HTTP_REFERER']);
    }

    public function testModifyTable()
    {
        // Vps_Component_Cache wird neu geschrieben, danach kann man das besser testen
        $this->markTestIncomplete();

        $this->_cache->expects($this->once())
            ->method('remove')
            ->with($this->equalTo(array('root-referer'=>$this->_root->getChildComponent('-referer'))));

        $m = Vps_Model_Abstract::getInstance('Vpc_Advanced_SearchEngineReferer_Referer_Model');
        $r = $m->createRow();
        $r->name = 'foo12';
        $r->component_id = 'root';
        $r->save();

        Vps_Component_RowObserver::getInstance()->process();
    }

    public function testComponentNewEntry()
    {
        $this->markTestIncomplete();
        $ref2 = $this->_root->getChildComponent('-referer2')->getComponent();
        $model = $ref2->getModel();
        $oldRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals($oldRow->id, 2);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo2';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals($oldRow->id, $newRow->id);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=fooNew';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals(6, $newRow->id);

        $_SERVER['HTTP_REFERER'] = 'http://www.google.at/search?hl=de&q=foo2';
        $ref2->processInput();
        $newRow = $model->getRow($model->select()
            ->whereEquals('component_id', 'root')
            ->order('id', 'DESC')
        );
        $this->assertEquals(7, $newRow->id);
    }
}
